<?php declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\APIKey;
use App\Entity\PlayerJoin;
use App\Tests\FunctionalTestsTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @internal
 * @covers \App\Controller\ApiController
 */
class ApiControllerTest extends WebTestCase
{
    use FunctionalTestsTrait;

    private APIKey $apiKey;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->setEntityManager(self::$kernel->getContainer());
        $this->truncateEntities();

        $this->apiKey = (new APIKey())
            ->setActive(true)
            ->setOwner(1)
            ->setKey('fake_api_key')
        ;

        $this->em->persist($this->apiKey);
        $this->em->flush();
    }

    public function testQueryWithoutApiKeyReturns403(): void
    {
        $this->client->request('GET', '/api/query');

        self::assertResponseStatusCodeSame(403);
    }

    public function testQueryForIPWithNoResults(): void
    {
        $this->client->request(
            'GET',
            '/api/query',
            [
                'query' => '127.0.0.1',
            ],
            server: [
                'HTTP_X-API-KEY' => $this->apiKey->getKey(),
            ],
        );

        self::assertResponseIsSuccessful();
        $response = $this->client->getResponse()->getContent() ?: '';

        self::assertEquals(
            <<<'RES'
            Results of IP address lookup for 127.0.0.1:
              No results found
            RES,
            rtrim($response)
        );
    }

    public function testQueryForIPWithTwoCallsigns(): void
    {
        $this->addPlayerJoin('allejo', '127.0.0.1');
        $this->addPlayerJoin('allejo', '127.0.0.1');
        $this->addPlayerJoin('not allejo', '127.0.0.1');

        $this->client->request(
            'GET',
            '/api/query',
            [
                'query' => '127.0.0.1',
            ],
            server: [
                'HTTP_X-API-KEY' => $this->apiKey->getKey(),
            ],
        );

        self::assertResponseIsSuccessful();
        $response = $this->client->getResponse()->getContent() ?: '';

        self::assertEquals(
            <<<'RES'
            Results of IP address lookup for 127.0.0.1:
              allejo (2 times)
              not allejo (1 times)
            RES,
            rtrim($response)
        );
    }

    public function testQueryForCallsignWithNoResults(): void
    {
        $this->client->request(
            'GET',
            '/api/query',
            [
                'query' => 'allejo',
            ],
            server: [
                'HTTP_X-API-KEY' => $this->apiKey->getKey(),
            ],
        );

        self::assertResponseIsSuccessful();
        $response = $this->client->getResponse()->getContent() ?: '';

        self::assertEquals(
            <<<'RES'
            Results of callsign lookup for allejo:
              No results found
            RES,
            rtrim($response)
        );
    }

    public function testQueryForCallsignWithSingleCallsignAndMultipleIPs(): void
    {
        $this->addPlayerJoin('allejo', '127.0.0.1');
        $this->addPlayerJoin('allejo', '127.0.0.1');
        $this->addPlayerJoin('allejo', '127.0.0.2');

        $this->client->request(
            'GET',
            '/api/query',
            [
                'query' => 'allejo',
            ],
            server: [
                'HTTP_X-API-KEY' => $this->apiKey->getKey(),
            ],
        );

        self::assertResponseIsSuccessful();
        $response = $this->client->getResponse()->getContent() ?: '';

        self::assertEquals(
            <<<'RES'
            Results of callsign lookup for allejo:
              127.0.0.1:
                allejo (2 times)
              127.0.0.2:
                allejo (1 times)
            RES,
            rtrim($response)
        );
    }

    public function testQueryForCallsignWithMultipleCallsignAndMultipleIPs(): void
    {
        $this->addPlayerJoin('allejo', '127.0.0.1');
        $this->addPlayerJoin('allejo', '127.0.0.1');
        $this->addPlayerJoin('allejo', '127.0.0.2');
        $this->addPlayerJoin('not allejo', '127.0.0.2');
        $this->addPlayerJoin('not allejo', '127.0.0.2');

        $this->client->request(
            'GET',
            '/api/query',
            [
                'query' => 'allejo',
            ],
            server: [
                'HTTP_X-API-KEY' => $this->apiKey->getKey(),
            ],
        );

        self::assertResponseIsSuccessful();
        $response = $this->client->getResponse()->getContent() ?: '';

        self::assertEquals(
            <<<'RES'
            Results of callsign lookup for allejo:
              127.0.0.1:
                allejo (2 times)
              127.0.0.2:
                allejo (1 times)
                not allejo (2 times)
            RES,
            rtrim($response)
        );
    }

    public function testReportJoinUpdatesCount(): void
    {
        $this->client->request(
            'POST',
            '/api/report-join',
            [
                'callsign' => 'allejo',
                'bzid' => '123456',
                'ipaddress' => '8.8.8.8',
                'build' => '2.4.24.20220319-MAINT-mac64xc1330-SDL2',
            ],
            server: [
                'HTTP_X-API-KEY' => $this->apiKey->getKey(),
            ],
        );

        self::assertResponseIsSuccessful();
        $response = $this->client->getResponse()->getContent() ?: '';

        self::assertEquals(
            'SUCCESS: Added join for "allejo" (BZID: 123456) from 8.8.8.8',
            $response
        );

        $joinRepo = $this->getEntityManager()->getRepository(PlayerJoin::class);
        $callsigns = $joinRepo->findUniqueJoinsByIP('8.8.8.8');

        self::assertCount(1, $callsigns);
        self::assertEquals('allejo', $callsigns[0]['callsign']);
    }
}

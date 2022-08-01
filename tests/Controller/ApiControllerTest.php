<?php declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Address;
use App\Entity\APIKey;
use App\Entity\Callsign;
use App\Entity\PlayerJoin;
use App\Entity\RawLog;
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
        $this->em = self::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;

        $this->truncateEntities([
            Address::class,
            APIKey::class,
            Callsign::class,
            PlayerJoin::class,
            RawLog::class,
        ]);

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
        $this->client->request('GET', '/api/query', [
            'apikey' => $this->apiKey->getKey(),
            'query' => '127.0.0.1',
        ]);

        self::assertResponseIsSuccessful();

        $response = $this->client->getResponse()->getContent();
        self::assertEquals(
            <<<'RES'
            Results of IP address lookup for 127.0.0.1:
              No results found
            RES,
            trim($response)
        );
    }

    public function testQueryForIPWithTwoCallsigns(): void
    {
        $this->addPlayerJoin('allejo', '127.0.0.1');
        $this->addPlayerJoin('allejo', '127.0.0.1');
        $this->addPlayerJoin('not allejo', '127.0.0.1');

        $this->client->request('GET', '/api/query', [
            'apikey' => $this->apiKey->getKey(),
            'query' => '127.0.0.1',
        ]);

        self::assertResponseIsSuccessful();

        $response = $this->client->getResponse()->getContent();
        self::assertEquals(
            <<<'RES'
            Results of IP address lookup for 127.0.0.1:
              allejo (2 times)
              not allejo (1 times)
            RES,
            trim($response)
        );
    }
}

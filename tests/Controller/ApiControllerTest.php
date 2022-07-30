<?php

namespace App\Tests\Controller;

use App\Controller\ApiController;
use App\Entity\Address;
use App\Entity\APIKey;
use App\Entity\Callsign;
use App\Entity\PlayerJoin;
use App\Entity\RawLog;
use App\Tests\FunctionalTestsTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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
            ->getManager();

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
            ->setKey('fake_api_key');

        $this->em->persist($this->apiKey);
        $this->em->flush();
    }

    public function testQueryWithoutApiKeyReturns403(): void
    {
        $this->client->request('GET', '/api/query');

        self::assertResponseStatusCodeSame(403);
    }
}

<?php declare(strict_types=1);

namespace App\Tests;

use App\Entity\Address;
use App\Entity\Callsign;
use App\Entity\PlayerJoin;
use App\Entity\RawLog;
use App\Service\RawLogService;
use DateTime;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

trait FunctionalTestsTrait
{
    protected EntityManagerInterface $em;

    protected function addPlayerJoin(string $callsign, string $ipAddress, string $hostname = 'home-network.local', string $bzid = null): void
    {
        $service = new RawLogService(
            $this->em->getRepository(Address::class),
            $this->em->getRepository(Callsign::class),
            $this->em->getRepository(PlayerJoin::class),
        );

        $rawLogEntry = new RawLog();
        $rawLogEntry->setCallsign($callsign);
        $rawLogEntry->setHostname($hostname);
        $rawLogEntry->setIpAddress($ipAddress);
        $rawLogEntry->setApikey($this->apiKey);
        $rawLogEntry->setBzid($bzid);
        $rawLogEntry->setBuild('BZFlag-2.4.22-macOS');
        $rawLogEntry->setEventTime(new DateTime());

        $this->em->persist($rawLogEntry);
        $service->updatePlayerData($rawLogEntry);
        $this->em->flush();
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->em;
    }

    protected function setEntityManager(ContainerInterface $container): void
    {
        $this->em = $container->get('doctrine')->getManager(); // @phpstan-ignore-line
    }

    protected function truncateEntities(): void
    {
        $purger = new ORMPurger($this->getEntityManager());
        $purger->purge();
    }
}

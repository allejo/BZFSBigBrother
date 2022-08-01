<?php declare(strict_types=1);

namespace App\Tests;

use App\Entity\Address;
use App\Entity\Callsign;
use App\Entity\PlayerJoin;
use App\Entity\RawLog;
use App\Service\RawLogService;
use DateTime;
use Doctrine\DBAL\Exception as DoctrineException;
use Doctrine\ORM\EntityManagerInterface;

trait FunctionalTestsTrait
{
    protected EntityManagerInterface $em;

    protected function addPlayerJoin(string $callsign, string $ipAddress, string $hostname = 'home-network.local', string $bzid = null): void
    {
        static $service;

        if ($service === null) {
            $addressRepo = $this->em->getRepository(Address::class);
            $callsignRepo = $this->em->getRepository(Callsign::class);
            $joinRepo = $this->em->getRepository(PlayerJoin::class);

            $service = new RawLogService($addressRepo, $callsignRepo, $joinRepo);
        }

        $rawLogEntry = new RawLog();
        $rawLogEntry->setCallsign($callsign);
        $rawLogEntry->setHostname($hostname);
        $rawLogEntry->setIpAddress($ipAddress);
        $rawLogEntry->setApikey($this->apiKey);
        $rawLogEntry->setBzid($bzid);
        $rawLogEntry->setBuild('BZFlag-2.4.22-macOS');
        $rawLogEntry->setEventTime(new DateTime());

        $service->updatePlayerData($rawLogEntry);
        $this->em->persist($rawLogEntry);
        $this->em->flush();
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->em;
    }

    /**
     * @param array<class-string> $entities
     *
     * @see https://symfonycasts.com/screencast/phpunit/control-database
     *
     * @throws DoctrineException
     */
    protected function truncateEntities(array $entities): void
    {
        $connection = $this->getEntityManager()->getConnection();
        $databasePlatform = $connection->getDatabasePlatform();

        if ($databasePlatform->supportsForeignKeyConstraints()) {
            $connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');
        }

        foreach ($entities as $entity) {
            $query = $databasePlatform->getTruncateTableSQL(
                $this->getEntityManager()->getClassMetadata($entity)->getTableName()
            );
            $connection->executeQuery($query);
        }

        if ($databasePlatform->supportsForeignKeyConstraints()) {
            $connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');
        }
    }
}

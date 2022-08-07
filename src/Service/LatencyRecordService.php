<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Address;
use App\Entity\APIKey;
use App\Entity\LatencyRecord;
use App\Entity\ServerInstance;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * @phpstan-type LatencyRecordForm array{lag: string, jitter: string, packetloss: string, ipaddress: string, time: string}
 */
class LatencyRecordService
{
    private APIKey $apiKey;

    private string $hostname;

    private int $port;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function getHostname(): string
    {
        return $this->hostname;
    }

    public function setHostname(string $hostname): void
    {
        $this->hostname = $hostname;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function setPort(int $port): void
    {
        $this->port = $port;
    }

    public function getApiKey(): APIKey
    {
        return $this->apiKey;
    }

    public function setApiKey(APIKey $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @param LatencyRecordForm[] $records
     */
    public function recordLatency(array $records): void
    {
        $serverInstance = $this->getServerInstance();

        foreach ($records as $record) {
            $address = $this->getAddress($record['ipaddress']);

            if ($address === null) {
                $this->logger->error(sprintf('Could not find Address record for IP %s' . $record['ipaddress']));

                continue;
            }

            try {
                $dateTime = new DateTime($record['time']);
            } catch (Exception $e) {
                $this->logger->error(sprintf('Could not parse string (%s) as DateTime: %s', $record['time'], $e->getMessage()));

                continue;
            }

            $entity = new LatencyRecord();
            $entity->setServerInstance($serverInstance);
            $entity->setAddress($address);
            $entity->setLag((int)$record['lag']);
            $entity->setJitter((int)$record['jitter']);
            $entity->setPacketLoss((float)$record['packetloss']);
            $entity->setEventTime($dateTime);

            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();
    }

    private function getAddress(string $ipAddress): ?Address
    {
        return $this->entityManager->getRepository(Address::class)->findOneBy([
            'ipAddress' => $ipAddress,
        ], ['id' => 'DESC']);
    }

    private function getServerInstance(): ServerInstance
    {
        $repo = $this->entityManager->getRepository(ServerInstance::class);
        $serverInstance = $repo->findOneBy([
            'hostname' => $this->getHostname(),
            'port' => $this->getPort(),
        ]);

        if ($serverInstance === null) {
            $serverInstance = new ServerInstance();
            $serverInstance->setHostname($this->getHostname());
            $serverInstance->setPort($this->getPort());
            $serverInstance->setApiKey($this->getApiKey());

            $this->entityManager->persist($serverInstance);
        }

        return $serverInstance;
    }
}

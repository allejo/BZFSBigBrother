<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Address;
use App\Entity\Callsign;
use App\Entity\PlayerJoin;
use App\Entity\RawLog;
use App\Repository\AddressRepository;
use App\Repository\CallsignRepository;
use App\Repository\PlayerJoinRepository;

class RawLogService
{
    public function __construct(private readonly AddressRepository $addressRepository, private readonly CallsignRepository $callsignRepository, private readonly PlayerJoinRepository $playerJoinRepository)
    {
    }

    public function updatePlayerData(RawLog $rawLog): bool
    {
        $address = $this->getAddress($rawLog->getIpAddress(), $rawLog->getHostname());
        $callsign = $this->getCallsign($rawLog->getCallsign());

        $join = new PlayerJoin();
        $join->setAddress($address);
        $join->setCallsign($callsign);
        $join->setEventTime($rawLog->getEventTime());

        $this->playerJoinRepository->add($join);

        return true;
    }

    private function getAddress(string $ipAddress, string $hostname): Address
    {
        $existing = $this->addressRepository->findOneBy([
            'ipAddress' => $ipAddress,
            'hostname' => $hostname,
        ], ['id' => 'DESC']);

        if ($existing === null) {
            $entity = new Address();
            $entity->setIpAddress($ipAddress);
            $entity->setHostname($hostname);

            $this->addressRepository->add($entity);

            return $entity;
        }

        return $existing;
    }

    private function getCallsign(string $callsign): Callsign
    {
        $existing = $this->callsignRepository->findOneBy([
            'callsign' => $callsign,
        ]);

        if ($existing === null) {
            $entity = new Callsign();
            $entity->setCallsign($callsign);

            $this->callsignRepository->add($entity);

            return $entity;
        }

        return $existing;
    }
}

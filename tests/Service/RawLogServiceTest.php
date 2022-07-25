<?php

namespace App\Tests\Service;

use App\Entity\Address;
use App\Entity\APIKey;
use App\Entity\Callsign;
use App\Entity\PlayerJoin;
use App\Entity\RawLog;
use App\Repository\AddressRepository;
use App\Repository\CallsignRepository;
use App\Repository\PlayerJoinRepository;
use App\Service\RawLogService;
use DateTime;
use PHPUnit\Framework\TestCase;

class RawLogServiceTest extends TestCase
{
    public function testUpdatePlayerDataWithNoExistingAddressOrCallsign(): void
    {
        $rawLogEntry = new RawLog();
        $rawLogEntry->setCallsign('allejo');
        $rawLogEntry->setHostname('home-network.local');
        $rawLogEntry->setIpAddress('192.168.1.2');
        $rawLogEntry->setApikey($this->createMock(APIKey::class));
        $rawLogEntry->setBzid('12345');
        $rawLogEntry->setBuild('BZFlag-2.4.22-macOS');
        $rawLogEntry->setEventTime(new DateTime());

        /** @var Address[] $addresses */
        $addresses = [];
        $addressRepo = $this->createMock(AddressRepository::class);
        $addressRepo
            ->method('findOneBy')
            ->willReturn(null);
        $addressRepo
            ->method('add')
            ->willReturnCallback(static function() use ($rawLogEntry, &$addresses) {
                $address = new Address();
                $address->setIpAddress($rawLogEntry->getIpAddress());
                $address->setHostname($rawLogEntry->getHostname());

                $addresses[] = $address;
            });

        /** @var Callsign[] $callsigns */
        $callsigns = [];
        $callsignRepo = $this->createMock(CallsignRepository::class);
        $callsignRepo
            ->method('findOneBy')
            ->willReturn(null);
        $callsignRepo
            ->method('add')
            ->willReturnCallback(static function () use ($rawLogEntry, &$callsigns) {
                $callsign = new Callsign();
                $callsign->setCallsign($rawLogEntry->getCallsign());

                $callsigns[] = $callsign;
            });

        /** @var PlayerJoin[] $joins */
        $joins = [];
        $joinRepo = $this->createMock(PlayerJoinRepository::class);
        $joinRepo
            ->method('add')
            ->willReturnCallback(static function () use (&$addresses, &$callsigns, &$joins) {
                $join = new PlayerJoin();
                $join->setAddress($addresses[0]);
                $join->setCallsign($callsigns[0]);

                $joins[] = $join;
            });

        $service = new RawLogService($addressRepo, $callsignRepo, $joinRepo);
        $service->updatePlayerData($rawLogEntry);

        $this->assertCount(1, $addresses);
        $this->assertCount(1, $callsigns);
        $this->assertCount(1, $joins);

        $this->assertEquals($rawLogEntry->getIpAddress(), $joins[0]->getAddress()->getIpAddress());
        $this->assertEquals($rawLogEntry->getHostname(), $joins[0]->getAddress()->getHostname());
        $this->assertEquals($rawLogEntry->getCallsign(), $joins[0]->getCallsign()->getCallsign());
    }
}

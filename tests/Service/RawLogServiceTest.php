<?php declare(strict_types=1);

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
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \App\Service\RawLogService
 */
class RawLogServiceTest extends TestCase
{
    public function testUpdatePlayerDataWithNoExistingAddressOrCallsign(): void
    {
        $rawLogEntry = $this->createMockRawLog([]);

        /** @var Address[] $addresses */
        $addresses = [];

        /** @var Callsign[] $callsigns */
        $callsigns = [];

        /** @var PlayerJoin[] $joins */
        $joins = [];

        $addressRepo = $this->createMockRepository(AddressRepository::class, $addresses, hasAdd: true);
        $callsignRepo = $this->createMockRepository(CallsignRepository::class, $callsigns, hasAdd: true);
        $joinRepo = $this->createMockRepository(PlayerJoinRepository::class, $joins, hasAdd: true);

        $service = new RawLogService($addressRepo, $callsignRepo, $joinRepo);
        $service->updatePlayerData($rawLogEntry);

        $this->assertCount(1, $addresses);
        $this->assertCount(1, $callsigns);
        $this->assertCount(1, $joins);

        $this->assertEquals($rawLogEntry->getIpAddress(), $joins[0]->getAddress()->getIpAddress());
        $this->assertEquals($rawLogEntry->getHostname(), $joins[0]->getAddress()->getHostname());
        $this->assertEquals($rawLogEntry->getCallsign(), $joins[0]->getCallsign()->getCallsign());
    }

    public function testUpdatePlayerDataWithExistingAddressOnly(): void
    {
        $rawLogEntry = $this->createMockRawLog([
            'callsign' => 'not allejo',
            'bzid' => null,
        ]);

        /** @var Address[] $addresses */
        $addresses = [
            (new Address())
                ->setIpAddress($rawLogEntry->getIpAddress())
                ->setHostname($rawLogEntry->getHostname()),
        ];

        /** @var Callsign[] $callsigns */
        $callsigns = [];

        /** @var PlayerJoin[] $joins */
        $joins = [];

        $addressRepo = $this->createMockRepository(AddressRepository::class, $addresses, hasFindOneBy: true);
        $callsignRepo = $this->createMockRepository(CallsignRepository::class, $callsigns, hasAdd: true);
        $joinRepo = $this->createMockRepository(PlayerJoinRepository::class, $joins, hasAdd: true);

        $service = new RawLogService($addressRepo, $callsignRepo, $joinRepo);
        $service->updatePlayerData($rawLogEntry);

        $this->assertCount(1, $addresses);
        $this->assertEquals($addresses[0], $joins[0]->getAddress());
    }

    public function testUpdatePlayerDataWithExistingAddressAndCallsign(): void
    {
        $rawLogEntry = $this->createMockRawLog([]);

        /** @var Address[] $addresses */
        $addresses = [
            (new Address())
                ->setIpAddress($rawLogEntry->getIpAddress())
                ->setHostname($rawLogEntry->getHostname()),
        ];

        /** @var Callsign[] $callsigns */
        $callsigns = [
            (new Callsign())
                ->setCallsign($rawLogEntry->getCallsign()),
        ];

        /** @var PlayerJoin[] $joins */
        $joins = [];

        $addressRepo = $this->createMockRepository(AddressRepository::class, $addresses, hasFindOneBy: true);
        $callsignRepo = $this->createMockRepository(CallsignRepository::class, $callsigns, hasFindOneBy: true);
        $joinRepo = $this->createMockRepository(PlayerJoinRepository::class, $joins, hasAdd: true);

        $service = new RawLogService($addressRepo, $callsignRepo, $joinRepo);
        $service->updatePlayerData($rawLogEntry);

        $this->assertCount(1, $addresses);
        $this->assertCount(1, $callsigns);
        $this->assertEquals($addresses[0], $joins[0]->getAddress());
        $this->assertEquals($callsigns[0], $joins[0]->getCallsign());
    }

    /**
     * @param array<string, mixed> $options
     */
    private function createMockRawLog(array $options): RawLog
    {
        $rawLogEntry = new RawLog();
        $rawLogEntry->setCallsign('allejo');
        $rawLogEntry->setHostname('home-network.local');
        $rawLogEntry->setIpAddress('192.168.1.2');
        $rawLogEntry->setApikey($this->createMock(APIKey::class));
        $rawLogEntry->setBzid('12345');
        $rawLogEntry->setBuild('BZFlag-2.4.22-macOS');
        $rawLogEntry->setEventTime(new DateTime());

        foreach ($options as $key => $value) {
            $fxn = 'set' . ucfirst($key);
            $rawLogEntry->{$fxn}($value);
        }

        return $rawLogEntry;
    }

    /**
     * @template T of EntityRepository
     * @template V of object
     *
     * @param class-string<T>  $class
     * @param array<number, V> $storage
     *
     * @return T
     */
    private function createMockRepository(string $class, array &$storage, bool $hasFindOneBy = false, bool $hasAdd = false)
    {
        $repository = $this->createMock($class);

        if ($hasFindOneBy) {
            $repository
                ->method('findOneBy')
                ->willReturn($storage[0] ?? null)
            ;
        }

        if ($hasAdd) {
            $repository
                ->method('add')
                ->willReturnCallback(static function ($entity) use (&$storage) {
                    $storage[] = $entity;
                })
            ;
        }

        return $repository;
    }
}

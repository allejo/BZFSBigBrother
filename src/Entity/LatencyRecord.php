<?php declare(strict_types=1);

namespace App\Entity;

use App\Repository\LatencyRecordRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LatencyRecordRepository::class)]
class LatencyRecord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Address $address = null;

    #[ORM\Column]
    private ?int $jitter = null;

    #[ORM\Column(name: 'lag_ms')]
    private ?int $lag = null;

    #[ORM\Column]
    private ?float $packetLoss = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $eventTime = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ServerInstance $serverInstance = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getJitter(): ?int
    {
        return $this->jitter;
    }

    public function setJitter(int $jitter): self
    {
        $this->jitter = $jitter;

        return $this;
    }

    public function getLag(): ?int
    {
        return $this->lag;
    }

    public function setLag(int $lag): self
    {
        $this->lag = $lag;

        return $this;
    }

    public function getPacketLoss(): ?float
    {
        return $this->packetLoss;
    }

    public function setPacketLoss(float $packetLoss): self
    {
        $this->packetLoss = $packetLoss;

        return $this;
    }

    public function getEventTime(): ?DateTimeInterface
    {
        return $this->eventTime;
    }

    public function setEventTime(DateTimeInterface $eventTime): self
    {
        $this->eventTime = $eventTime;

        return $this;
    }

    public function getServerInstance(): ?ServerInstance
    {
        return $this->serverInstance;
    }

    public function setServerInstance(?ServerInstance $serverInstance): self
    {
        $this->serverInstance = $serverInstance;

        return $this;
    }
}

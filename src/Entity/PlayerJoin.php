<?php declare(strict_types=1);

namespace App\Entity;

use App\Repository\PlayerJoinRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'joins')]
#[ORM\Index(name: 'when_INDEX', columns: ['event_time'])]
#[ORM\Entity(repositoryClass: PlayerJoinRepository::class)]
class PlayerJoin
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private readonly int $id;

    #[ORM\ManyToOne(targetEntity: \App\Entity\Callsign::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?\App\Entity\Callsign $callsign = null;

    #[ORM\ManyToOne(targetEntity: \App\Entity\Address::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?\App\Entity\Address $address = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $eventTime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCallsign(): ?Callsign
    {
        return $this->callsign;
    }

    public function setCallsign(?Callsign $callsign): self
    {
        $this->callsign = $callsign;

        return $this;
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

    public function getEventTime(): ?DateTimeInterface
    {
        return $this->eventTime;
    }

    public function setEventTime(?DateTimeInterface $eventTime): self
    {
        $this->eventTime = $eventTime;

        return $this;
    }
}

<?php declare(strict_types=1);

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="joins",
 *     indexes={
 *         @ORM\Index(
 *             name="when_INDEX",
 *             columns={"eventtime"}
 *         )
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\PlayerJoinRepository")
 */
class PlayerJoin
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue()
     */
    private $id;

    /**
     * @var null|Callsign
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Callsign")
     * @ORM\JoinColumn(nullable=false)
     */
    private $callsign;

    /**
     * @var null|Address
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Address")
     * @ORM\JoinColumn(nullable=false)
     */
    private $address;

    /**
     * @var null|DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
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

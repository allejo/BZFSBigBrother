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
 * @ORM\Entity
 */
class PlayerJoin
{
    /**
     * @var int
     *
     * @ORM\Column(name="joinid", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var null|int
     *
     * @ORM\Column(name="callsignid", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $callsignid;

    /**
     * @var null|int
     *
     * @ORM\Column(name="addressid", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $addressid;

    /**
     * @var null|DateTime
     *
     * @ORM\Column(name="eventtime", type="datetime", nullable=true)
     */
    private $eventTime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCallsignid(): ?int
    {
        return $this->callsignid;
    }

    public function setCallsignid(?int $callsignid): self
    {
        $this->callsignid = $callsignid;

        return $this;
    }

    public function getAddressid(): ?int
    {
        return $this->addressid;
    }

    public function setAddressid(?int $addressid): self
    {
        $this->addressid = $addressid;

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

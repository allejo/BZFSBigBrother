<?php declare(strict_types=1);

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="rawlog",
 *     indexes={
 *         @ORM\Index(
 *             name="apikey",
 *             columns={"apikey"}
 *         )
 *     }
 * )
 * @ORM\Entity
 */
class RawLog
{
    /**
     * @var int
     *
     * @ORM\Column(name="rawlogid", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var null|string
     *
     * @ORM\Column(name="callsign", type="string", length=32, nullable=true)
     */
    private $callsign;

    /**
     * @var null|string
     *
     * @ORM\Column(name="bzid", type="string", length=32, nullable=true)
     */
    private $bzid;

    /**
     * @var null|string
     *
     * @ORM\Column(name="ipaddress", type="string", length=15, nullable=true)
     */
    private $ipAddress;

    /**
     * @var null|string
     *
     * @ORM\Column(name="hostname", type="string", length=255, nullable=true)
     */
    private $hostname;

    /**
     * @var string
     *
     * @ORM\Column(name="apikey", type="string", length=40, nullable=false)
     */
    private $apikey;

    /**
     * @var null|DateTime
     *
     * @ORM\Column(name="eventtime", type="datetime", nullable=true)
     */
    private $eventTime;

    /**
     * @var null|string
     *
     * @ORM\Column(name="build", type="string", length=255, nullable=true)
     */
    private $build;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCallsign(): ?string
    {
        return $this->callsign;
    }

    public function setCallsign(?string $callsign): self
    {
        $this->callsign = $callsign;

        return $this;
    }

    public function getBzid(): ?string
    {
        return $this->bzid;
    }

    public function setBzid(?string $bzid): self
    {
        $this->bzid = $bzid;

        return $this;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(?string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    public function getHostname(): ?string
    {
        return $this->hostname;
    }

    public function setHostname(?string $hostname): self
    {
        $this->hostname = $hostname;

        return $this;
    }

    public function getApikey(): ?string
    {
        return $this->apikey;
    }

    public function setApikey(string $apikey): self
    {
        $this->apikey = $apikey;

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

    public function getBuild(): ?string
    {
        return $this->build;
    }

    public function setBuild(?string $build): self
    {
        $this->build = $build;

        return $this;
    }
}

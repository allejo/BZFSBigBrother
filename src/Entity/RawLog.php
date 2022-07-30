<?php declare(strict_types=1);

namespace App\Entity;

use App\Repository\RawLogRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'rawlog')]
#[ORM\Index(name: 'apikey', columns: ['apikey_id'])]
#[ORM\Entity(repositoryClass: RawLogRepository::class)]
class RawLog
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private readonly int $id;

    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    private ?string $callsign = null;

    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    private ?string $bzid = null;

    #[ORM\Column(type: 'string', length: 15, nullable: true)]
    private ?string $ipAddress = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $hostname = null;

    #[ORM\ManyToOne(targetEntity: APIKey::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?APIKey $apikey = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $eventTime;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $build = null;

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

    public function getApikey(): ?APIKey
    {
        return $this->apikey;
    }

    public function setApikey(APIKey $apikey): self
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

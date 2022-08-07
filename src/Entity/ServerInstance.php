<?php declare(strict_types=1);

namespace App\Entity;

use App\Repository\ServerInstanceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServerInstanceRepository::class)]
class ServerInstance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $hostname = null;

    #[ORM\Column]
    private ?int $port = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?APIKey $apiKey = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHostname(): ?string
    {
        return $this->hostname;
    }

    public function setHostname(string $hostname): self
    {
        $this->hostname = $hostname;

        return $this;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function setPort(int $port): self
    {
        $this->port = $port;

        return $this;
    }

    public function getApiKey(): ?APIKey
    {
        return $this->apiKey;
    }

    public function setApiKey(?APIKey $apiKey): self
    {
        $this->apiKey = $apiKey;

        return $this;
    }
}

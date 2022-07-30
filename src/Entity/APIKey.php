<?php declare(strict_types=1);

namespace App\Entity;

use App\Repository\APIKeyRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * API Key to allow for querying and writing to our tables.
 */
#[ORM\Table(name: 'apikeys')]
#[ORM\UniqueConstraint(name: 'key', columns: ['key'])]
#[ORM\Index(name: 'active', columns: ['active'])]
#[ORM\Index(name: 'owner', columns: ['owner'])]
#[ORM\Entity(repositoryClass: APIKeyRepository::class)]
class APIKey
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private readonly int $id;

    #[ORM\Column(type: 'string', length: 40, nullable: false)]
    private ?string $key = null;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private ?bool $active = null;

    #[ORM\Column(type: 'integer', nullable: false, options: ['unsigned' => true])]
    private ?int $owner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getOwner(): ?int
    {
        return $this->owner;
    }

    public function setOwner(int $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}

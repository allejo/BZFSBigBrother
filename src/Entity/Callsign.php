<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'callsigns')]
#[ORM\UniqueConstraint(name: 'callsign_UNIQUE', columns: ['callsign'])]
#[ORM\Entity(repositoryClass: \App\Repository\CallsignRepository::class)]
class Callsign
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private readonly int $id;

    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    private ?string $callsign = null;

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
}

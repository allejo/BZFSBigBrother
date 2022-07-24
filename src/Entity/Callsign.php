<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="callsigns",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(
 *             name="callsign_UNIQUE",
 *             columns={"callsign"}
 *         )
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\CallsignRepository")
 */
class Callsign
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var null|string
     *
     * @ORM\Column(name="callsign", type="string", length=32, nullable=true)
     */
    private $callsign;

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

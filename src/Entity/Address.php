<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * An address is an IP + Host combination.
 *
 * @ORM\Table(
 *     name="addresses",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(
 *             name="iphost_INDEX",
 *             columns={"ipaddress", "hostname"}
 *         )
 *     },
 *     indexes={
 *         @ORM\Index(
 *             name="hostname_INDEX",
 *             columns={"hostname"}
 *         )
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\AddressRepository")
 */
class Address
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
     * @var null|string
     *
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $ipAddress;

    /**
     * @var null|string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hostname;

    public function getId(): ?int
    {
        return $this->id;
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
}

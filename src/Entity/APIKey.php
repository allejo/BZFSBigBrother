<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * API Key to allow for querying and writing to our tables.
 *
 * @ORM\Table(
 *     name="apikeys",
 *     uniqueConstraints={
 *        @ORM\UniqueConstraint(
 *            name="key",
 *            columns={"apikey"}
 *        )
 *     },
 *     indexes={
 *         @ORM\Index(
 *             name="active",
 *             columns={"active"}
 *         ),
 *         @ORM\Index(
 *             name="owner",
 *             columns={"owner"}
 *         )
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\APIKeyRepository")
 */
class APIKey
{
    /**
     * @var int
     *
     * @ORM\Column(name="apikeyid", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="apikey", type="string", length=40, nullable=false)
     */
    private $key;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active;

    /**
     * @var int
     *
     * @ORM\Column(name="owner", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $owner;

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

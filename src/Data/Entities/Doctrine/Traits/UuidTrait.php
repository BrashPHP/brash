<?php

namespace App\Data\Entities\Doctrine\Traits;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\PrePersist;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

trait UuidTrait
{
    #[Column(type: 'uuid', unique: true)]
    private ?UuidInterface $uuid = null;

    #[PrePersist]
    public function generateUuid(): void
    {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * Get the internal primary identity key.
     */
    public function getUuid(): ?UuidInterface
    {
        return $this->uuid;
    }

    /**
     * Set the internal primary identity key.
     */
    public function setUuid(UuidInterface|string|null $uuid): self
    {
        $this->uuid = is_string($uuid) ? Uuid::fromString($uuid) : $uuid;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace App\Data\Entities\Cycle\Traits;

use Cycle\Annotated\Annotation\Column;

trait TimestampsTrait
{

    #[Column(type: 'datetime')]
    private ?\DateTime $createdAt = null;

    #[Column(type: 'datetime', nullable: true, name: 'updated_at')]
    private ?\DateTime $updated = null;

    public function setUpdated(?\DateTime $dateTime): self
    {
        // WILL be saved in the database
        $this->updated = $dateTime;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdated(): ?\DateTime
    {
        return $this->updated;
    }
}

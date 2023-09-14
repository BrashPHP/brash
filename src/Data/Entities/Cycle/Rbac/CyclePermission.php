<?php

namespace App\Data\Entities\Cycle\Rbac;

use App\Data\Entities\Cycle\Traits\TimestampsTrait;
use App\Data\Entities\Cycle\Traits\UuidTrait;
use App\Domain\Models\RBAC\Permission;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\Annotated\Annotation\Table\Index;
use Cycle\ORM\Entity\Behavior\{CreatedAt, UpdatedAt};
use Cycle\ORM\Entity\Behavior\Uuid\Uuid4;

#[Entity]
#[Uuid4]
#[CreatedAt(
    field: 'createdAt',
    column: 'created_at'
)]
#[UpdatedAt(
    field: 'updated',
    column: 'updated_at'
)]
#[Index(columns: ['name'], unique: true)]
class CyclePermission
{

    use TimestampsTrait, UuidTrait;

    #[Column(type: "primary")]
    public int $id;
    #[Column(type: 'string', nullable: false)]
    public string $name;
    #[Column(type: 'string', nullable: false)]
    public string $context;

    #[BelongsTo(target: CycleRole::class, nullable: false)]
    public CycleRole $role;

    #[BelongsTo(target: CycleResource::class, nullable: false)]
    public CycleResource $resource;

    public static function fromModel(Permission $permission)
    {
        return (new self())
            ->setContext($permission->intent->value)
            ->setCreatedAt($permission->createdAt)
            ->setName($permission->name)
            ->setUpdated($permission->updatedAt);
    }

    public function getContext(): string
    {
        return $this->context;
    }

    public function setContext(string $context): self
    {
        $this->context = $context;

        return $this;
    }


    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getRole(): CycleRole
    {
        return $this->role;
    }

    public function setRole(CycleRole $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getResource(): CycleResource
    {
        return $this->resource;
    }

    public function setResource(CycleResource $resource): self
    {
        $this->resource = $resource;

        return $this;
    }
}
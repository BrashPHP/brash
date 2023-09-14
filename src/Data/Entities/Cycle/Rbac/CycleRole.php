<?php

namespace App\Data\Entities\Cycle\Rbac;

use App\Data\Entities\Cycle\Traits\TimestampsTrait;
use App\Data\Entities\Cycle\Traits\UuidTrait;
use App\Domain\Models\RBAC\Role;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table\Index;
use Cycle\ORM\Entity\Behavior\{CreatedAt, UpdatedAt};
use Cycle\ORM\Entity\Behavior\Uuid\Uuid4;
use Cycle\Annotated\Annotation\Relation\HasMany;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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
class CycleRole
{
    use TimestampsTrait, UuidTrait;
    #[Column(type: "primary")]
    public int $id;
    #[Column(type: 'string', nullable: false)]
    public string $name;
    #[Column(type: 'string', nullable: false, default: '')]
    public string $description;
    #[Column(type: 'boolean', nullable: false, typecast: 'bool')]
    public bool $isActive = true;

    #[HasMany(target: CycleRole::class, nullable: true, collection: 'doctrine')]
    public Collection $extendedRoles;

    #[HasMany(target: CyclePermission::class, nullable: true, collection: 'doctrine')]
    public Collection $permissions;

    public function __construct()
    {
        $this->permissions = new ArrayCollection();
        $this->extendedRoles = new ArrayCollection();
    }

    public static function fromModel(Role $role)
    {
        return (new self())
            ->setCreatedAt($role->createdAt)
            ->setDescription($role->description)
            ->setIsActive($role->isActive)
            ->setName($role->name)
            ->setUpdated($role->updatedAt)
            ->setExtendedRoles(
                new ArrayCollection(
                    array_map(
                        fn(Role $role) => static::fromModel($role),
                        $role->extendedRoles
                    )
                )
            );
    }

    public function getDescription(): string
    {
        return $this->description;
    }


    public function setDescription(string $description): self
    {
        $this->description = $description;

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


    public function getIsActive(): bool
    {
        return $this->isActive;
    }


    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getExtendedRoles(): Collection
    {
        return $this->extendedRoles;
    }

    public function setExtendedRoles(Collection $extendedRoles): self
    {
        $this->extendedRoles = $extendedRoles;
        return $this;
    }

    public function extendRole(CycleRole $role): void
    {
        $this->extendedRoles->add($role);
    }

    public function removeRole(CycleRole $permission): void
    {
        $this->extendedRoles = $this->extendedRoles->filter(static fn(CycleRole $p) => $p !== $post);
    }

    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function setPermissions(Collection $permissions): self
    {
        $this->permissions = $permissions;
        return $this;
    }

    public function addPermission(CyclePermission $permission): void
    {
        $this->permissions->add($permission);
    }

    public function removePermission(CyclePermission $permission): void
    {
        $this->permissions = $this->permissions->filter(static fn(CyclePermission $p) => $p !== $post);
    }
}
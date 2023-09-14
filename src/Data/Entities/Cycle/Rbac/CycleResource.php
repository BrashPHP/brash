<?php

namespace App\Data\Entities\Cycle\Rbac;

use App\Data\Entities\Cycle\Traits\TimestampsTrait;
use App\Data\Entities\Cycle\Traits\UuidTrait;
use App\Domain\Models\RBAC\Resource;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\HasMany;
use Cycle\Annotated\Annotation\Table\Index;
use Cycle\ORM\Entity\Behavior\{CreatedAt, UpdatedAt};
use Cycle\ORM\Entity\Behavior\Uuid\Uuid4;
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
class CycleResource
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

    #[HasMany(target: CyclePermission::class, nullable: true, collection: 'doctrine')]
    public Collection $permissions;

    public function __construct()
    {
        $this->permissions = new ArrayCollection();
    }

    public static function fromModel(Resource $resource)
    {
        return (new self())
            ->setCreatedAt($resource->createdAt)
            ->setDescription($resource->description)
            ->setIsActive($resource->isActive)
            ->setName($resource->name)
            ->setUpdated($resource->updatedAt);
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

    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    /**
     * @param Collection $permissions
     */
    public function setPermissions(Collection $permissions): self
    {
        $this->permissions = $permissions;

        return $this;
    }

    /**
     * @param CyclePermission $permission
     */
    public function addPermission(CyclePermission $permission): void
    {
        $this->permissions->add($permission);

        $permission->setResource($this);
    }

    /**
     * @param CyclePermission $permission
     */
    public function removePermission(CyclePermission $permission): void
    {
        $this->permissions = $this->permissions->filter(static fn(CyclePermission $p) => $p !== $post);
    }
}
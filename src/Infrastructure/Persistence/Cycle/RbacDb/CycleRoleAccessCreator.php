<?php
namespace App\Infrastructure\Persistence\Cycle\RbacDb;

use App\Data\Entities\Cycle\Rbac\CyclePermission;
use App\Data\Entities\Cycle\Rbac\CycleResource;
use App\Data\Entities\Cycle\Rbac\CycleRole;
use App\Domain\Models\RBAC\Permission;
use App\Domain\Models\RBAC\Resource;
use App\Domain\Models\RBAC\Role;
use App\Domain\OptionalApi\Result;
use App\Domain\OptionalApi\Result\Err;
use App\Domain\OptionalApi\Result\Ok;
use Cycle\ORM\EntityManager;
use Cycle\ORM\ORM;


class CycleRoleAccessCreator
{
    public function __construct(
        private ORM $orm
    ) {
    }

    /**
     * @return Result<CycleRole>
     */
    public function create(
        Role $role,
        Resource $resource,
        Permission $permission
    ): Result {
        try {
            $t = new EntityManager($this->orm);
            $cycleRole = CycleRole::fromModel($role);
            $cycleResource = CycleResource::fromModel($resource);
            $cyclePermission = CyclePermission::fromModel($permission)
                ->setResource($cycleResource)
                ->setRole($cycleRole);

            $cycleRole->addPermission($cyclePermission);

            $t->persist($cycleRole);

            $t->run();

            return new Ok($cycleRole);
        } catch (\Throwable $th) {
            return new Err($th);
        }
    }
}
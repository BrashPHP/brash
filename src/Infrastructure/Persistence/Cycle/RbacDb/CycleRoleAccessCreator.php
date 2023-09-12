<?php
namespace App\Infrastructure\Persistence\Cycle\RbacDb;

use App\Data\Entities\Cycle\Rbac\CyclePermission;
use App\Data\Entities\Cycle\Rbac\CycleResource;
use App\Data\Entities\Cycle\Rbac\CycleRole;
use App\Domain\Models\RBAC\AccessControl;
use App\Domain\Models\RBAC\ContextIntent;
use App\Domain\Models\RBAC\Permission;
use App\Domain\Models\RBAC\Resource;
use App\Domain\Models\RBAC\Role;
use Cycle\ORM\EntityManager;
use Cycle\ORM\ORM;


class CycleRoleAccessCreator
{
    public function __construct(
        private ORM $orm,
        private AccessControl $accessControl
    ) {
    }

    public function create(
        Role $role,
        Resource $resource,
        Permission $permission
    ): Role {
        $t = new EntityManager($this->orm);
        $cycleRole = new CycleRole();
        $cycleRole
            ->setName("resource_owner_1")
            ->setDescription("Resource Owner Role")
            ->setIsActive(true);

        $cycleResource = new CycleResource();
        $cycleResource->setName("museum")->setDescription("museum description");

        $cyclePermission = new CyclePermission();
        $cyclePermission
            ->setContext($permission->intent->value)
            ->setName("permission_name")
            ->setResource($cycleResource)->setRole($cycleRole);

        $t->persist($cyclePermission);

        $t->run();

        $this->accessControl
            ->appendRole($role)
            ->appendResource($resource)
            ->grantAccessOn($role, $resource, [$permission]);

        return $this->accessControl->getRole($role)->get();
    }
}
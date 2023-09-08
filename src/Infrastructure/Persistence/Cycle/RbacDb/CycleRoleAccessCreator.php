<?php
namespace App\Infrastructure\Persistence\Cycle\RbacDb;

use App\Data\Entities\Cycle\Rbac\CyclePermission;
use App\Data\Entities\Cycle\Rbac\CycleResource;
use App\Data\Entities\Cycle\Rbac\CycleRole;
use App\Domain\Models\RBAC\ContextIntent;
use App\Domain\Models\RBAC\Permission;
use App\Domain\Models\RBAC\Resource;
use App\Domain\Models\RBAC\Role;
use Cycle\ORM\EntityManager;
use Cycle\ORM\ORM;


class CycleRoleAccessCreator
{
    public function __construct(private ORM $orm)
    {
    }

    public function create(): Role
    {
        $t = new EntityManager($this->orm);
        $role = new CycleRole();
        $role
            ->setName("resource_owner_1")
            ->setDescription("Resource Owner Role")
            ->setIsActive(true);

        $resource = new CycleResource();
        $resource->setName("museum")->setDescription("museum description");

        $permission = new CyclePermission();
        $permission
            ->setContext('READ')
            ->setName("permission_name")
            ->setResource($resource)->setRole($role);

        $t->persist($permission);

        $t->run();

        $roleObject = new Role("resource_owner", "Resource Owner Role");
        $resource = new Resource('image', 'images resources');
        $canCreate = new Permission('can:create', ContextIntent::READ);
        $roleObject->addPermissionToResource($canCreate, $resource);

        return $roleObject;
    }
}
<?php
namespace App\Infrastructure\Persistence\Cycle\RbacDb;

use App\Data\Entities\Cycle\Rbac\CycleRole;
use App\Domain\Models\RBAC\Resource;
use App\Domain\Models\RBAC\Role;
use App\Domain\OptionalApi\Result;
use App\Domain\OptionalApi\Result\Err;
use App\Domain\OptionalApi\Result\Ok;
use Cycle\ORM\ORM;

class CycleRoleAccessRepository
{
    public function __construct(private ORM $orm)
    {
    }

    /**
     * @return Result<CycleRole,\Exception>
     */
    public function getRoleWithPermissions(Role $role, Resource $resource): Result
    {
        try {
            $roleDb = $this->orm
                ->getRepository(CycleRole::class)
                ->select()
                ->load(["permissions", "extendedRoles"])
                ->where('name', $role->name)
                ->fetchOne();

            return new Ok($roleDb);
        } catch (\Throwable $th) {
            return new Err($th);
        }
    }

    /**
     * @return Result<CycleRole,\Exception>
     */
    public function getOneByName(string $subject): Result
    {
        try {
            return new Ok(
                $this->orm->getRepository(CycleRole::class)
                    ->select()
                    ->where('name', $subject)
                    ->fetchOne()
            );
        } catch (\Throwable $th) {
            return new Err($th);

        }
    }
}
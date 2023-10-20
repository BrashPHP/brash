<?php

namespace App\Infrastructure\Persistence\Cycle\RbacDb;

use App\Data\Entities\Cycle\Rbac\CycleRole;
use App\Domain\Models\RBAC\Role;
use App\Domain\OptionalApi\Result;
use App\Domain\OptionalApi\Result\Err;
use App\Domain\OptionalApi\Result\Ok;
use Cycle\ORM\EntityManager;
use Cycle\ORM\ORM;

final class CycleRoleExtender
{
    public function __construct(
        private ORM $orm
    ) {
    }

    /**
     * @return Result<CycleRole>
     */
    public function extend(
        Role $target,
        Role $parent
    ): Result {
        try {
            $t = new EntityManager($this->orm);
            $repository = new CycleRoleAccessRepository($this->orm);
            $cycleRole = $repository
                ->getOneByName($target->name)
                ->ok()
                ->map(fn($el) => $el ?? CycleRole::fromModel($target))
                ->get();
            $cycleParentRole = $repository
                ->getOneByName($parent->name)
                ->ok()
                ->map(fn($el) => $el ?? CycleRole::fromModel($parent))
                ->get();

            $cycleRole->extendRole($cycleParentRole);

            $t->persist($cycleRole);

            $t->run();

            return new Ok($cycleRole);
        } catch (\Throwable $th) {
            dd($th);
            return new Err($th);
        }
    }
}
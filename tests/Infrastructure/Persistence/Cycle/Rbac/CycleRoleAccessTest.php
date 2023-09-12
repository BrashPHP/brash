<?php

namespace Tests\Infrastructure\Persistence\Orm\Rbac;

use App\Domain\Models\RBAC\AccessControl;
use App\Domain\Models\RBAC\ContextIntent;
use App\Domain\Models\RBAC\Permission;
use App\Domain\Models\RBAC\Resource;
use App\Domain\Models\RBAC\Role;
use App\Infrastructure\Persistence\Cycle\RbacDb\CycleRoleAccessCreator;
use Cycle\ORM\EntityManager;
use Cycle\ORM\ORM;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[CoversNothing]
#[Group('cycleorm')]
final class CycleRoleAccessTest extends TestCase
{
    private CycleRoleAccessCreator $sut;
    private EntityManager $em;
    private ORM $orm;

    public static function setUpBeforeClass(): void
    {
        self::createDatabase();
    }

    public static function tearDownAfterClass(): void
    {
        self::truncateDatabase();
    }

    public function setUp(): void
    {
        $container = $this->getContainer();
        $this->em = $container->get(EntityManager::class);
        $this->orm = $container->get(ORM::class);
        $this->sut = new CycleRoleAccessCreator($this->orm, new AccessControl());
    }

    // protected function tearDown(): void
    // {
    //     $collection = $this->orm->getRepository(DoctrineAccount::class)->findAll();
    //     foreach ($collection as $c) {
    //         $this->em->delete($c);
    //     }
    //     $this->em->run();
    // }

    public function testShouldInsertRole()
    {
        $roleObject = new Role("resource_owner", "Resource Owner Role");
        $resource = new Resource('image', 'images resources');
        $canCreate = new Permission('can:create', ContextIntent::READ);
        $role = $this->sut->create(
            $roleObject,
            $resource,
            $canCreate
        );

        dd($role);
    }
}
<?php

namespace Tests\Infrastructure\Persistence\Orm\Rbac;

use App\Domain\Models\RBAC\ContextIntent;
use App\Domain\Models\RBAC\Permission;
use App\Domain\Models\RBAC\Resource;
use App\Infrastructure\Persistence\Cycle\RbacDb\CycleRoleAccessCreator;
use App\Infrastructure\Persistence\Cycle\RbacDb\CycleRoleAccessRepository;
use Cycle\ORM\EntityManager;
use Cycle\ORM\ORM;
use Tests\TestCase;

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
        $this->sut = new CycleRoleAccessCreator($this->orm);
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
        $role = $this->sut->create();

        dd($role);
    }
}
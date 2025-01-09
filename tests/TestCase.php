<?php

declare(strict_types=1);

namespace Tests;

use Brash\Framework\Data\BehaviourComponents\DatabaseCleaner;
use Brash\Framework\Data\BehaviourComponents\DatabaseCreator;
use PHPUnit\Framework\TestCase as PHPUnit_TestCase;
use Tests\Traits\App\AppTestTrait;
use Tests\Traits\App\InstanceManagerTrait;
use Tests\Traits\App\RequestManagerTrait;

/**
 * @internal
 *
 * @coversNothing
 */
abstract class TestCase extends PHPUnit_TestCase
{
    use AppTestTrait;
    use InstanceManagerTrait;
    use RequestManagerTrait;

    public static function createDatabase()
    {
        $container = self::requireContainer();

        DatabaseCreator::create($container);
    }

    final public static function truncateDatabase()
    {
        $container = self::requireContainer();

        DatabaseCleaner::truncate($container);
    }

    public static function createDatabaseDoctrine()
    {
        $container = self::requireContainer();

        DatabaseCreator::createDoctrineDatabase($container);
    }

    final public static function truncateDatabaseDoctrine()
    {
        $container = self::requireContainer();

        DatabaseCleaner::truncateDoctrineDatabase($container);
    }
}

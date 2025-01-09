<?php

namespace Core\Data\BehaviourComponents;

use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Psr\Container\ContainerInterface;

class DatabaseCleaner
{
    public static function truncate(ContainerInterface $containerInterface): void
    {
        DatabaseCleaner::truncateDoctrineDatabase($containerInterface);
    }

    public static function truncateDoctrineDatabase(ContainerInterface $containerInterface): void
    {
        /** @var EntityManager */
        $entityManager = $containerInterface->get(EntityManager::class);
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->dropDatabase();
    }
}

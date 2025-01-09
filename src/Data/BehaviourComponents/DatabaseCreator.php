<?php

namespace Brash\Framework\Data\BehaviourComponents;

use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Psr\Container\ContainerInterface;

class DatabaseCreator
{
    public static function create(ContainerInterface $containerInterface): void
    {

        DatabaseCreator::createDoctrineDatabase($containerInterface);
    }

    public static function createDoctrineDatabase(ContainerInterface $containerInterface): void
    {
        $entityManager = $containerInterface->get(EntityManager::class);
        $metadatas = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->updateSchema($metadatas);
    }
}

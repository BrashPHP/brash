<?php

namespace Core\Data\Doctrine;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\ORMSetup;

class EntityManagerBuilder
{
    public static function produce(array $doctrine): EntityManagerInterface
    {
        $devMode = $doctrine['dev_mode'];
        $metadata_dirs = $doctrine['metadata_dirs'];

        $config = ORMSetup::createAttributeMetadataConfiguration(
            $metadata_dirs,
            $devMode
        );
        $config->setIdentityGenerationPreferences(
            [
            PostgreSQLPlatform::class => ClassMetadata::GENERATOR_TYPE_SEQUENCE,
            ]
        );
        $connectionFactory = new DoctrineConnectionFactory();
        $connection = $connectionFactory->createConnectionFromArray($doctrine, $config);

        $entityManager = new EntityManager($connection, $config);

        if (!Type::hasType('uuid_binary')) {
            Type::addType('uuid_binary', 'Ramsey\Uuid\Doctrine\UuidBinaryType');
            $entityManager->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('uuid_binary', 'binary');
        }

        return $entityManager;
    }
}

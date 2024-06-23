<?php

namespace Core\Data\OrmFactories;

use Core\Data\Domain\ConnectionModel;
use Psr\Container\ContainerInterface;
use Cycle\Database\Config\DriverConfig;
use function Core\functions\inTesting;
use Cycle\Database\Config\DatabaseConfig;
use Core\Data\Cycle\Facade\ConnectorFacade;
use Cycle\ORM;
use Cycle\Database\Config;
use Cycle\Database\DatabaseManager;
use Symfony\Component\Finder\Finder;
use Spiral\Tokenizer\ClassLocator;
use Cycle\Schema;
use Cycle\Annotated;
use Cycle\ORM\Entity\Behavior\EventDrivenCommandGenerator;
use Cycle\ORM\EntityManager as CycleEntityManager;

final class CycleOrmFactory
{
    public function create(): array
    {
        return [
            DatabaseManager::class => fn(ContainerInterface $containerInterface)
                => $this->createDbManager($containerInterface),

            ORM\ORM::class => fn(ContainerInterface $containerInterface)
                => $this->createORM($containerInterface),

            CycleEntityManager::class => static fn(
                ContainerInterface $container
            ) => new CycleEntityManager($container->get(ORM\ORM::class)),
        ];
    }

    private function createDbManager(ContainerInterface $container): DatabaseManager
    {
        return new DatabaseManager(
            new DatabaseConfig(
                [
                    "default" => "default",
                    "databases" => [
                        "default" => [
                            "connection" => inTesting() ? "sqlite" : "production",
                        ],
                    ],
                    "connections" => [
                        "sqlite" => new Config\SQLiteDriverConfig(
                            connection: new Config\SQLite\MemoryConnectionConfig(),
                            queryCache: true
                        ),
                        "production" => $this->createDriverConfig($container),
                    ],
                ]
            )
        );
    }

    private function createOrm(ContainerInterface $containerInterface)
    {
        $root = $containerInterface->get('root');

        $finder = (new Finder())
            ->files()
            ->in(
                [
                    $root . "/src/Data/Entities/Cycle",
                    $root . "/src/Data/Entities/Cycle/Rbac",
                ]
            );
        $classLocator = new ClassLocator($finder);
        $database = $containerInterface->get(DatabaseManager::class);
        $schemaCompiler = new Schema\Compiler();

        $schema = $schemaCompiler->compile(
            new Schema\Registry($database),
            [
                new Schema\Generator\ResetTables(),
                // re-declared table schemas (remove columns)
                new Annotated\Embeddings($classLocator),
                // register embeddable entities
                new Annotated\Entities($classLocator),
                // register annotated entities
                new Annotated\TableInheritance(),
                // register STI/JTI
                new Annotated\MergeColumns(),
                // add @Table column declarations
                new Schema\Generator\GenerateRelations(),
                // generate entity relations
                new Schema\Generator\GenerateModifiers(),
                // generate changes from schema modifiers
                new Schema\Generator\ValidateEntities(),
                // make sure all entity schemas are correct
                new Schema\Generator\RenderTables(),
                // declare table schemas
                new Schema\Generator\RenderRelations(),
                // declare relation keys and indexes
                new Schema\Generator\RenderModifiers(),
                // render all schema modifiers
                new Annotated\MergeIndexes(),
                // add @Table column declarations
                new Schema\Generator\SyncTables(),
                // sync table changes to database
                new Schema\Generator\GenerateTypecast(), // typecast non string columns
            ]
        );
        $schema = new ORM\Schema($schema);
        $commandGenerator = new EventDrivenCommandGenerator(
            $schema,
            $containerInterface
        );

        $ormFactory = new ORM\Factory($database);
        $ormFactory = $ormFactory->withCollectionFactory(
            'doctrine',
            // Alias
            new ORM\Collection\DoctrineCollectionFactory,
            \Doctrine\Common\Collections\Collection::class // <= Base collection
        );

        return new ORM\ORM(
            $ormFactory,
            $schema,
            $commandGenerator
        );
    }

    private function createDriverConfig(ContainerInterface $container): ?DriverConfig
    {
        if (!inTesting()) {
            $connectorFacade = new ConnectorFacade(
                connection: $container->get(ConnectionModel::class)?->getAsArray(),
                connectionOptions: []
            );

            // Configure connector as you wish
            $connectorFacade
                ->configureFactory()
                ->withQueryCache(true)
                ->withSchema("public");

            return $connectorFacade->produceDriverConnection(
                driverOptions: []
            );
        }

        return null;
    }
}

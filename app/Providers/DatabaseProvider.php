<?php

declare(strict_types=1);

namespace Core\Providers;

use Core\Data\OrmFactories\{CycleOrmFactory, DoctrineOrmFactory};
use DI\ContainerBuilder;
use Core\Providers\AppProviderInterface;


class DatabaseProvider implements AppProviderInterface
{
    public function provide(ContainerBuilder $container)
    {
        $cycleDbFactory = new CycleOrmFactory();
        $doctrineDbFactories = new DoctrineOrmFactory();

        $container->addDefinitions(
            [
                ...$cycleDbFactory->create(),
                ...$doctrineDbFactories->create()
            ]
        );
    }
}

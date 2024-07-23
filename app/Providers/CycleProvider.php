<?php

declare(strict_types=1);

namespace Core\Providers;

use Core\Data\OrmFactories\CycleOrmFactory;
use DI\ContainerBuilder;
use Core\Providers\AppProviderInterface;


class CycleProvider implements AppProviderInterface
{
    public function provide(ContainerBuilder $container)
    {
        $cycleDbFactory = new CycleOrmFactory();

        $container->addDefinitions(
            $cycleDbFactory->create()
        );
    }
}

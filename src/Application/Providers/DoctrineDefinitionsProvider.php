<?php

namespace App\Application\Providers;

use Core\Providers\AppProviderInterface;
use DI\ContainerBuilder;

final class DoctrineDefinitionsProvider implements AppProviderInterface
{
    public function provide(ContainerBuilder $container)
    {
        $src = dirname(dirname(__DIR__));
        $container->addDefinitions([
            'doctrine_metadata_dirs' => ["{$src}/Data/Entities/Doctrine",]
        ]);
    }
}

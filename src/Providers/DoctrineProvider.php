<?php

namespace Brash\Framework\Providers;

use Brash\Framework\Data\Domain\ConnectionModel;
use Brash\Framework\Data\OrmFactories\DoctrineOrmFactory;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

use function Brash\Framework\functions\isDev;

class DoctrineProvider implements AppProviderInterface
{
    public function provide(ContainerBuilder $container): void
    {
        $doctrineDbFactories = new DoctrineOrmFactory;

        $container->addDefinitions([
            ...$this->createDoctrineSettings(),
            ...$doctrineDbFactories->create(),
        ]);
    }

    private function createDoctrineSettings(): array
    {
        return [
            'doctrine' => static function (ContainerInterface $c): array {
                $currentWorkingDirectory = getcwd();
                $metadata_dirs = $c->has('doctrine_metadata_dirs') ? $c->get('doctrine_metadata_dirs') : [];

                return [
                    // if true, metadata caching is forcefully disabled
                    'dev_mode' => isDev(),

                    // path where the compiled metadata info will be cached
                    // make sure the path exists and it is writable
                    'cache_dir' => $currentWorkingDirectory.'/var/doctrine',

                    // you should add any other path containing annotated entity classes
                    'metadata_dirs' => $metadata_dirs,

                    'connection' => $c->get(ConnectionModel::class),
                ];
            },
        ];
    }
}

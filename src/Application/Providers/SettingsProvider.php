<?php

namespace App\Application\Providers;

use Core\Data\Domain\ConnectionModel;
use DI\ContainerBuilder;
use Core\Providers\AppProviderInterface;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use function Core\functions\isDev;
use function Core\functions\isProd;

class SettingsProvider implements AppProviderInterface
{
    protected string $target = 'settings';

    public function provide(ContainerBuilder $container)
    {
        $container->addDefinitions($this->createSettings());
    }

    private function createSettings(): array
    {
        $root = dirname(dirname(__DIR__));

        return [
            'root' => $root,
            'temp' => "{$root}/tmp",
            'public' => "{$root}/public",
            'settings' => [
                'displayErrorDetails' => true,
                // Should be set to false in production
                'logger' => [
                    'name' => 'kitsune',
                    'path' => (getenv('docker') || !getenv('log-file')) ? 'php://stdout' : "{$root}/temp/logs/app.log",
                    'level' => isProd() ? Logger::INFO : Logger::DEBUG,
                ],
                'doctrine' => static function (ContainerInterface $c) use ($root): array {
                    return [
                        // if true, metadata caching is forcefully disabled
                        'dev_mode' => isDev(),

                        // path where the compiled metadata info will be cached
                        // make sure the path exists and it is writable
                        'cache_dir' => "{$root}/../var/doctrine",

                        // you should add any other path containing annotated entity classes
                        'metadata_dirs' => ["{$root}/Data/Entities/Doctrine"],

                        'connection' => $c->get(ConnectionModel::class),
                    ];
                },
            ],
        ];
    }
}

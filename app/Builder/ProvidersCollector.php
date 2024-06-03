<?php

namespace Core\Builder;

use Core\Providers\AppProviderInterface;
use Core\Providers\ConnectionProvider;
use Core\Providers\DatabaseProvider;
use Core\Providers\DependenciesProvider;
use Core\Providers\RepositoriesProvider;
use Core\Providers\ServicesProvider;
use Core\Providers\StartupProvider;
use Core\Providers\WorkerProvider;
use Core\Providers\SettingsProvider;
use DI\ContainerBuilder;

class ProvidersCollector
{
    /**
     * @var class-string<AppProviderInterface> $providers
     */
    public array $providers = [
        StartupProvider::class,
        ConnectionProvider::class,
        DatabaseProvider::class,
        DependenciesProvider::class,
        RepositoriesProvider::class,
        ServicesProvider::class,
        SettingsProvider::class,
        WorkerProvider::class,
    ];

    public function roll(ContainerBuilder $containerBuilder): void
    {
        foreach ($this->providers as $provider) {
            (new $provider())->provide($containerBuilder);
        }
    }
}

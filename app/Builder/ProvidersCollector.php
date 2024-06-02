<?php

namespace Core\Builder;

use Core\Providers\AppProviderInterface;
use Core\Providers\ConnectionProvider;
use Core\Providers\DatabaseProvider;
use Core\Providers\DependenciesProvider;
use Core\Providers\RepositoriesProvider;
use Core\Providers\ServicesProvider;
use Core\Providers\WorkerProvider;
use Core\Providers\SettingsProvider;
use DI\ContainerBuilder;

readonly class ProvidersCollector
{
    /**
     * @var class-string<AppProviderInterface> $providers
     */
    public array $providers = [
        ConnectionProvider::class,
        DatabaseProvider::class,
        DependenciesProvider::class,
        RepositoriesProvider::class,
        ServicesProvider::class,
        SettingsProvider::class,
        WorkerProvider::class,
    ];

    public function __construct(private ContainerBuilder $containerBuilder)
    {
    }

    public function roll(): ContainerBuilder
    {
        foreach ($this->providers as $provider) {
            (new $provider())->provide($this->containerBuilder);
        }

        return $this->containerBuilder;
    }
}

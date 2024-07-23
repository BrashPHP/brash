<?php

namespace Core\Builder;

use Core\Providers\AppProviderInterface;
use Core\Providers\ConnectionProvider;
use Core\Providers\CycleProvider;
use Core\Providers\DoctrineProvider;
use Core\Providers\LoggerProvider;
use Core\Providers\SettingsProvider;
use Core\Providers\StartupProvider;
use DI\ContainerBuilder;

class ProvidersCollector
{
    /**
     * @var class-string<AppProviderInterface>|AppProviderInterface $providers
     */
    public array $providers = [
        SettingsProvider::class,
        StartupProvider::class,
        LoggerProvider::class,
        ConnectionProvider::class,
        CycleProvider::class,
        DoctrineProvider::class,
    ];

    public function execute(ContainerBuilder $containerBuilder): void
    {
        foreach ($this->providers as $provider) {
            $objectProvider = $provider;
            if (is_string($provider)) {
                $objectProvider = new $provider();
            }
            $objectProvider->provide($containerBuilder);
        }
    }

    public function addProvider(string|AppProviderInterface $provider): void
    {
        $this->providers[] = $provider;
    }
}

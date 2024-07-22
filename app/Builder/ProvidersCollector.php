<?php

namespace Core\Builder;

use Core\Providers\AppProviderInterface;
use Core\Providers\ConnectionProvider;
use Core\Providers\DatabaseProvider;
use Core\Providers\StartupProvider;
use DI\ContainerBuilder;

class ProvidersCollector
{
    /**
     * @var class-string<AppProviderInterface>|AppProviderInterface $providers
     */
    public array $providers = [
        StartupProvider::class,
        ConnectionProvider::class,
        DatabaseProvider::class,
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

    public function addProvider(string|AppProviderInterface $provider): void{
        $this->providers[] = $provider;
    }
}

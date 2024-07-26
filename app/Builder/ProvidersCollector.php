<?php

namespace Core\Builder;

use Core\Providers\AppProviderInterface;
use DI\ContainerBuilder;

class ProvidersCollector
{
    /**
     * @var class-string<AppProviderInterface>|AppProviderInterface $providers
     */
    private array $providers = [];

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

    /**
     * Includes a provider to add entries to the container and boots application dependencies.
     *
     * @param class-string<AppProviderInterface>|\Core\Providers\AppProviderInterface $provider
     *
     * @return void
     */
    public function addProvider(string|AppProviderInterface $provider): void
    {
        $this->providers[] = $provider;
    }
}

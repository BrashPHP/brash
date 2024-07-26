<?php

namespace Core\Http\Factories;

use Core\Builder\ProvidersCollector;
use Core\Providers\AppProviderInterface;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Core\Providers\ConnectionProvider;
use Core\Providers\CycleProvider;
use Core\Providers\DoctrineProvider;
use Core\Providers\LoggerProvider;
use Core\Providers\SettingsProvider;
use Core\Providers\StartupProvider;


class ContainerFactory
{
    private ContainerBuilder $containerBuilder;
    private ProvidersCollector $providersCollector;
    private array $defaultProviders = [
        SettingsProvider::class,
        StartupProvider::class,
        LoggerProvider::class,
        ConnectionProvider::class,
        CycleProvider::class,
        DoctrineProvider::class,
    ];

    public function __construct(
        private bool $enableCompilation = false,
    ) {
        $this->containerBuilder = new ContainerBuilder();
        $this->providersCollector = new ProvidersCollector();
        foreach ($this->defaultProviders as $provider) {
            $this->providersCollector->addProvider($provider);
        }

        if ($this->enableCompilation) { // Should be set to true in production
            $this->containerBuilder->enableCompilation('tmp/var/cache');
        }
    }

    public function addProviders(AppProviderInterface|string ...$providers){
        foreach ($providers as $provider) {
            $this->providersCollector->addProvider($provider);
        }
    }

    public function get(): ContainerInterface
    {
        $this->providersCollector->execute($this->containerBuilder);

        return $this->containerBuilder->build();
    }
}

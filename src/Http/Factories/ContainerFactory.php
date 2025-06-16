<?php

namespace Brash\Framework\Http\Factories;

use Brash\Framework\Builder\ProvidersCollector;
use Brash\Framework\Providers\AppProviderInterface;
use Brash\Framework\Providers\ConnectionProvider;
use Brash\Framework\Providers\DoctrineProvider;
use Brash\Framework\Providers\LoggerProvider;
use Brash\Framework\Providers\SettingsProvider;
use Brash\Framework\Providers\StartupProvider;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

class ContainerFactory
{
    private readonly ContainerBuilder $containerBuilder;

    private readonly ProvidersCollector $providersCollector;

    private array $defaultProviders = [
        SettingsProvider::class,
        StartupProvider::class,
        LoggerProvider::class,
        ConnectionProvider::class,
        DoctrineProvider::class,
    ];

    public function __construct(
        private readonly bool $enableCompilation = false,
    ) {
        $this->containerBuilder = new ContainerBuilder;
        $this->providersCollector = new ProvidersCollector;
        foreach ($this->defaultProviders as $provider) {
            $this->providersCollector->addProvider($provider);
        }

        if ($this->enableCompilation) { // Should be set to true in production
            $this->containerBuilder->enableCompilation('tmp/var/cache');
        }
    }

    public function addProviders(AppProviderInterface|string ...$providers): void
    {
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

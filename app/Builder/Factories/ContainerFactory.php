<?php

namespace Core\Builder\Factories;

use Core\Builder\ProvidersCollector;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

class ContainerFactory
{
    private ContainerBuilder $containerBuilder;
    public function __construct(
        private bool $enableCompilation = false,
    ) {
        $this->containerBuilder = new ContainerBuilder();

        // if ($this->enableCompilation) { // Should be set to true in production
        //     $this->containerBuilder->enableCompilation('tmp/var/cache');
        // }
    }

    public function get(): ContainerInterface
    {
        $providersCollector = new ProvidersCollector();

        $providersCollector->roll($this->containerBuilder);

        return $this->containerBuilder->build();
    }
}

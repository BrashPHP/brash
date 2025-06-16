<?php

namespace Brash\Framework\Http\Factories;

use Brash\Framework\Builder\MiddlewareCollector;
use Brash\Framework\Http\Adapters\SlimFramework\SlimAppInterfaceBuilder;
use Brash\Framework\Http\Adapters\SlimFramework\SlimMiddlewareIncluder;
use Brash\Framework\Http\Adapters\SlimFramework\SlimRouteCollector;
use Brash\Framework\Http\Interfaces\ComponentsFactoryInterface;
use Brash\Framework\Http\Interfaces\ConfigurableApplicationInterface;
use Brash\Framework\Http\Interfaces\RouterInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Factory\AppFactory;

class SlimAppFactory implements ComponentsFactoryInterface
{
    private readonly App $app;

    public function __construct(private readonly ContainerInterface $container)
    {
        $this->app = AppFactory::create(container: $container);
    }

    public function createMiddlewareCollector(): MiddlewareCollector
    {
        return new MiddlewareCollector(
            new SlimMiddlewareIncluder($this->app),
        );
    }

    public function createRouterInterface(): RouterInterface
    {
        $routerCollectorFactory = new RouteCollectorFactory($this->container);

        return $routerCollectorFactory->getRouterCollector(
            new SlimRouteCollector(
                $this->app
            )
        );
    }

    public function createConfigurableApplicationInterface(): ConfigurableApplicationInterface
    {
        $this->app->addRoutingMiddleware();

        return new SlimAppInterfaceBuilder($this->app, $this->container->get(LoggerInterface::class));
    }
}

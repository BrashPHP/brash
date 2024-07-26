<?php

namespace Core\Http\Factories;

use Core\Builder\MiddlewareCollector;
use Core\Http\Adapters\SlimFramework\SlimAppInterfaceBuilder;
use Core\Http\Adapters\SlimFramework\SlimMiddlewareIncluder;
use Core\Http\Adapters\SlimFramework\SlimRouteCollector;
use Core\Http\Factories\RouteCollectorFactory;
use Core\Http\Interfaces\ComponentsFactoryInterface;
use Core\Http\Interfaces\ConfigurableApplicationInterface;
use Core\Http\Interfaces\RouterInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Factory\AppFactory;

class SlimAppFactory implements ComponentsFactoryInterface
{
    private App $app;

    public function __construct(private ContainerInterface $container)
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

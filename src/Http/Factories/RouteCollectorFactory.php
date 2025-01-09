<?php

namespace Brash\Framework\Http\Factories;

use Brash\Framework\Http\Interfaces\RouteCollectorInterface;
use Brash\Framework\Http\Middlewares\Factories\ValidationMiddlewareFactory;
use Brash\Framework\Http\Routing\Cache\GroupCacheResult;
use Brash\Framework\Http\Routing\GroupCollector;
use Brash\Framework\Http\Routing\RouteFactory;
use Brash\Framework\Http\Routing\RouteIncluder;
use Brash\Framework\Http\Routing\RouterCollector;
use Psr\Container\ContainerInterface;

class RouteCollectorFactory
{
    public function __construct(private ContainerInterface $containerInterface) {}

    public function getRouterCollector(RouteCollectorInterface $routeCollectorInterface): RouterCollector
    {
        $validationMiddlewareFactory = new ValidationMiddlewareFactory($this->containerInterface);
        $groupCollector = new GroupCollector;
        $caching = new GroupCacheResult;
        $routeFactory = new RouteFactory($groupCollector, $caching);
        $routeIncluder = new RouteIncluder(
            $routeCollectorInterface,
            $validationMiddlewareFactory
        );

        return new RouterCollector(
            $routeFactory,
            $routeIncluder
        );
    }
}

<?php

namespace Brash\Framework\Http\Factories;

use Brash\Framework\Http\Interfaces\RouteCollectorInterface;
use Brash\Framework\Http\Interfaces\RouterInterface;
use Brash\Framework\Http\Middlewares\Factories\ValidationMiddlewareFactory;
use Brash\Framework\Http\Routing\Cache\GroupCacheResult;
use Brash\Framework\Http\Routing\GroupCollector;
use Brash\Framework\Http\Routing\RouteFactory;
use Brash\Framework\Http\Routing\RouteIncluder;
use Brash\Framework\Http\Routing\RouterCollector;
use Psr\Container\ContainerInterface;

class RouteCollectorFactory
{
    public function __construct(
        private readonly ContainerInterface $containerInterface,
    ) {}

    public function getRouterCollector(RouteCollectorInterface $routeCollectorInterface): RouterInterface
    {
        $validationMiddlewareFactory = new ValidationMiddlewareFactory($this->containerInterface);
        $groupCollector = new GroupCollector;
        $caching = new GroupCacheResult;
        $routeFactory = new RouteFactory($groupCollector, $caching);
        $routeIncluder = new RouteIncluder(
            $routeCollectorInterface,
            $validationMiddlewareFactory
        );

        $actionsPath = $this->containerInterface->has('actions_path') ? $this->containerInterface->get('actions_path') : (
            getcwd().DIRECTORY_SEPARATOR.'src'
        );

        return new RouterCollector(
            $routeFactory,
            $routeIncluder,
            $actionsPath
        );
    }
}

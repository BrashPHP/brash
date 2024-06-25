<?php

namespace Core\Http\Routing;

use Core\Http\Domain\GroupModel;
use Core\Http\Domain\RouteModel;
use Core\Http\Attributes\Route as RouteAttribute;
use Core\Http\Interfaces\ActionInterface;
use Core\Http\Routing\Cache\GroupCacheResult;

final class RouteFactory
{
    public function __construct(
        private GroupCollector $groupCollector,
        private GroupCacheResult $cache
    ) {
    }

    public function make(RouteAttribute $routeAttribute, ActionInterface|string $controller): ?RouteModel
    {
        if ($routeAttribute->skip) {
            return null;
        }

        $middlewares = $this->extractMiddlewares($routeAttribute);
        $methods = is_array($routeAttribute->method) ? $routeAttribute->method : [$routeAttribute->method];
        $path = $routeAttribute->path;
        $group = $routeAttribute->group;

        if (!is_null($group)) {
            $groupResult = $this->getGroupResult($group);
            if ($groupResult->skip) {
                return null;
            }
            $path = implode("/", [trim($groupResult->prefix, "/"), trim(trim($path), "/")]);
            foreach ($middlewares as $middleware) {
                $groupResult->middlewares->push($middleware);
            }
            
            $middlewares =  $groupResult->middlewares;
        }

        return new RouteModel($methods, $path, $controller, $middlewares);
    }

    private function extractMiddlewares(RouteAttribute $routeAttribute): \SplStack
    {
        $middlewares = $routeAttribute->middleware;
        $stack = new \SplStack();

        if ($middlewares) {
            if (is_array($routeAttribute->middleware)) {
                foreach ($routeAttribute->middleware as $middleware) {
                    $stack->push($middleware);
                }
            }
            $stack->push($routeAttribute->middleware);
        }

        return $stack;
    }

    private function getGroupResult(object|string $group): GroupModel
    {
        $cached = $this->cache->get($group);

        if (is_null($cached)) {
            $newEntry = $this->groupCollector->getGroup($group);
            $this->cache->setCache($group, $newEntry);

            return $newEntry;
        }

        return $cached;
    }
}

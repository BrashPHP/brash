<?php

declare(strict_types=1);

namespace Core\Http;


use App\Presentation\RoutingColletor;
use Core\Attributes\Routing\Route as RouteAttribute;
use Core\Http\Abstractions\AbstractRouterTemplate;
use Core\Http\Interfaces\ActionInterface;
use Core\Http\Interfaces\RouteCollectorInterface;

class RouterCollector extends AbstractRouterTemplate
{
    public function defineRoutes(RouteCollectorInterface $collector, ActionInterface|string $controller): void
    {
        $reflector = new \ReflectionClass($controller);

        $routes = $reflector->getAttributes(RouteAttribute::class);

        # Use class_implements to verify rules and messages

        foreach ($routes as $route) {
            /** @var RouteAttribute */
            $routeAttribute = $route->newInstance();
            if ($routeAttribute->skip) {
                continue;
            }

            $methods = is_array($routeAttribute->method) ? $routeAttribute->method : [$routeAttribute->method];
            $path = $routeAttribute->path;
            $routeInterface = $collector->map($methods, $path, $controller);

            $hasMiddlewares = boolval($routeAttribute->middleware);
            if ($hasMiddlewares) {
                $middlewares = is_array($routeAttribute->middleware) ? $routeAttribute->middleware : [$routeAttribute->middleware];
                foreach ($middlewares as $middleware) {
                    $routeInterface->add($middleware);
                }
            }
        }
    }

    public function collect(RouteCollectorInterface $routeCollector): void
    {
        $controllerClasses = RoutingColletor::getActions();

        foreach ($controllerClasses as $controllerClass) {
            $this->defineRoutes($routeCollector, $controllerClass);
        }
    }
}

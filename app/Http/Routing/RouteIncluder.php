<?php

namespace Core\Http\Routing;

use Core\Http\Domain\RouteModel;
use Core\Http\Interfaces\RouteCollectorInterface;
use Core\Http\Interfaces\ValidationInterface;
use Core\Http\Middlewares\Factories\ValidationMiddlewareFactory;

class RouteIncluder
{
    public function __construct(
        private RouteCollectorInterface $routeCollectorInterface,
        private ValidationMiddlewareFactory $validationMiddlewareFactory
    ) {
    }

    public function include(RouteModel $route): void
    {
        $path = \str_ends_with($route->path, "/") ? \substr($route->path, 0, -1) : $route->path;
        echo "Path received from $path" . PHP_EOL;
        if (strlen($path) > 1) {
            $path = \str_starts_with($path, "/") ? \substr($path, 1) : $path;
        }

        $routeInterface = $this->routeCollectorInterface->map($route->methods, "/{$path}", $route->controller);

        foreach ($route->middlewares as $middleware) {
            $routeInterface->add($middleware);
        }

        if (in_array(ValidationInterface::class, class_implements($route->controller), true)) {
            $routeInterface->add($this->validationMiddlewareFactory->make($route->controller));
        }
    }
}

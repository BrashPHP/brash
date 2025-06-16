<?php

namespace Brash\Framework\Http\Routing;

use Brash\Framework\Http\Domain\RouteModel;
use Brash\Framework\Http\Interfaces\RouteCollectorInterface;
use Brash\Framework\Http\Interfaces\ValidationInterface;
use Brash\Framework\Http\Middlewares\Factories\ValidationMiddlewareFactory;

class RouteIncluder
{
    public function __construct(
        private readonly RouteCollectorInterface $routeCollectorInterface,
        private readonly ValidationMiddlewareFactory $validationMiddlewareFactory
    ) {}

    public function include(RouteModel $route): void
    {
        $path = \str_ends_with($route->path, '/') ? \substr($route->path, 0, -1) : $route->path;

        if (strlen($path) > 1) {
            $path = \str_starts_with($path, '/') ? \substr($path, 1) : $path;
        }

        $routeInterface = $this->routeCollectorInterface->map($route->methods, '/'.$path, $route->controller);

        foreach ($route->middlewares as $middleware) {
            $routeInterface->add($middleware);
        }

        if (in_array(ValidationInterface::class, class_implements($route->controller), true)) {
            $routeInterface->add($this->validationMiddlewareFactory->make($route->controller));
        }
    }
}

<?php

namespace Core\Http\Routing;

use Core\Http\Domain\RouteModel;
use Core\Http\Exceptions\HttpNotFoundException;
use Core\Http\Interfaces\RouteCollectorInterface;
use Core\Http\Interfaces\ValidationInterface;
use Core\Http\Middlewares\Factories\ValidationMiddlewareFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

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

    public function setDefaults(): void
    {
        $this->prepareOnTheFlyRequests();
        $this->setNotFound();
    }

    private function prepareOnTheFlyRequests()
    {
        $this->routeCollectorInterface->map(
            ['OPTIONS'],
            '/{routes:.+}',
            fn(Request $request, Response $response, $args) => $response->withStatus(200)
        );
    }
    private function setNotFound()
    {
        $this->routeCollectorInterface->map(
            ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'],
            '/{routes:.+}',
            function ($request) {
                throw new HttpNotFoundException($request);
            }
        );
    }
}

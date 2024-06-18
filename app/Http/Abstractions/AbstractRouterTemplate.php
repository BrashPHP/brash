<?php

declare(strict_types=1);

namespace Core\Http\Abstractions;


use Closure;
use Core\Http\Interfaces\RouteCollectorInterface;
use Core\Http\Interfaces\RouterInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;

abstract class AbstractRouterTemplate implements RouterInterface
{
    private RouteCollectorInterface $routeCollector;

    abstract public function collect(RouteCollectorInterface $routeCollector): void;

    public function run(RouteCollectorInterface $routeCollector): void
    {
        $this->routeCollector = $routeCollector;
        $this->prepareOnTheFlyRequests($routeCollector);
        $this->collect($this->routeCollector);
        $this->setNotFound($routeCollector);
    }

    private function prepareOnTheFlyRequests(RouteCollectorInterface $routeCollector)
    {
        $routeCollector->options(
            '/{routes:.+}',
            fn(Request $request, Response $response, $args) => $response->withStatus(200)
        );
    }

    private function setNotFound(RouteCollectorInterface $routeCollector)
    {
        $routeCollector->map(
            ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($request) {
                throw new HttpNotFoundException($request);
            }
        );
    }
}

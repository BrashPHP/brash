<?php

namespace Brash\Framework\Http\Adapters\SlimFramework;

use Brash\Framework\Http\Exceptions\HttpNotFoundException;
use Brash\Framework\Http\Interfaces\ApplicationInterface;
use Brash\Framework\Http\Interfaces\MiddlewareAttachableInterface;
use Brash\Framework\Http\Interfaces\RouteCollectorInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * App is a proxy to receive a request and emit a response, wrapping all the operations within the execution flow.
 * Currently, based on Slim's App.
 */
final class SlimAppAdapter implements ApplicationInterface
{
    private readonly RouteCollectorInterface $routeCollectorInterface;

    private bool $isStarted = false;

    public function __construct(private readonly \Slim\App $slimApp)
    {
        $this->routeCollectorInterface = new SlimRouteCollector($slimApp);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (! $this->isStarted) {
            $this->setDefaults();
        }

        return $this->slimApp->handle($request);
    }

    /**
     * Add route with multiple methods
     *
     * @param  string[]  $methods  Numeric array of HTTP method names
     * @param  string  $pattern  The route URI pattern
     * @param  callable|string  $callable  The route callback routine
     */
    public function map(array $methods, string $pattern, $callable): MiddlewareAttachableInterface
    {
        return $this->routeCollectorInterface->map($methods, $pattern, $callable);
    }

    public function setDefaults(): void
    {
        $this->prepareOnTheFlyRequests();
        $this->setNotFound();
        $this->isStarted = true;
    }

    private function prepareOnTheFlyRequests(): void
    {
        $this->routeCollectorInterface->map(
            ['OPTIONS'],
            '/{routes:.+}',
            fn (Request $request, Response $response, $args) => $response->withStatus(200)
        );
    }

    private function setNotFound(): void
    {
        $this->routeCollectorInterface->map(
            ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'],
            '/{routes:.+}',
            function ($request): never {
                throw new HttpNotFoundException($request);
            }
        );
    }
}

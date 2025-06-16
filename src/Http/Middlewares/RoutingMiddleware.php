<?php

namespace Brash\Framework\Http\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\App;
use Slim\Middleware\RoutingMiddleware as SlimRoutingMiddleware;

class RoutingMiddleware implements MiddlewareInterface
{
    private readonly SlimRoutingMiddleware $routingMiddleware;

    public function __construct(App $app)
    {
        $this->routingMiddleware = new SlimRoutingMiddleware(
            $app->getRouteResolver(),
            $app->getRouteCollector()->getRouteParser()
        );
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        return $this->routingMiddleware->process($request, $handler);
    }
}

<?php

namespace Core\Builder;

use Core\Http\Interfaces\MIddlewareIncluderInterface;
use App\Presentation\Middleware\JWTAuthMiddleware;
use App\Presentation\Middleware\SessionMiddleware;
use App\Presentation\Middleware\ResponseAdapterMiddleware;
use Middlewares\TrailingSlash;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Middleware\BodyParsingMiddleware;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Slim\Routing\RouteContext;

class MiddlewareCollector
{

    /**
     * @var class-string<Middleware> $providers
     */
    public array $middlewareClasses = [
        SessionMiddleware::class,
        JWTAuthMiddleware::class,
        BodyParsingMiddleware::class,
        ResponseAdapterMiddleware::class,
        //ErrorMiddleware::class
    ];

    public function __construct(MIddlewareIncluderInterface $root)
    {
        $root->add(new TrailingSlash());

        foreach ($this->middlewareClasses as $middlewareClass) {
            $root->add($middlewareClass);
        }
    }
}

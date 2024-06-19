<?php

namespace Core\Builder;

use Core\Http\Interfaces\MiddlewareIncluderInterface;
use App\Presentation\Middleware\JWTAuthMiddleware;
use App\Presentation\Middleware\SessionMiddleware;
use App\Presentation\Middleware\ResponseAdapterMiddleware;
use Core\Http\Middlewares\TrailingSlashMiddleware;
use Slim\Middleware\BodyParsingMiddleware;
use Psr\Http\Server\MiddlewareInterface as Middleware;


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
        // FiberMiddleware::class,
        //ErrorMiddleware::class
    ];

    public function collect(MiddlewareIncluderInterface $root)
    {
        $root->add(new TrailingSlashMiddleware());
        
        foreach ($this->middlewareClasses as $middlewareClass) {
            $root->add($middlewareClass);
        }
    }
}

<?php

namespace Core\Builder;

use App\Presentation\Middleware\JWTAuthMiddleware;
use App\Presentation\Middleware\ResponseAdapterMiddleware;
use App\Presentation\Middleware\SessionMiddleware;
use Core\Http\Interfaces\MIddlewareIncluderInterface;
use Core\ResourceLoader;
use Middlewares\TrailingSlash;
use Slim\Middleware\BodyParsingMiddleware;


class MiddlewareCollector
{
    public static function collect(MIddlewareIncluderInterface $root)
    {
        $root->add(new TrailingSlash());
        
        $middlewares = [
            SessionMiddleware::class,
            JWTAuthMiddleware::class,
            BodyParsingMiddleware::class,
            ResponseAdapterMiddleware::class,
            //ErrorMiddleware::class
        ];

        foreach ($middlewares as $middlewareClass) {
            $root->add($middlewareClass);
        }
    }
}
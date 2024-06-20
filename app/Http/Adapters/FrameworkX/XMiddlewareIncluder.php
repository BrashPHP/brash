<?php

namespace Core\Http\Adapters\FrameworkX;

use Core\Http\Interfaces\MiddlewareIncluderInterface;
use Psr\Http\Server\MiddlewareInterface;


class XMiddlewareIncluder implements MiddlewareIncluderInterface
{
    private array $middlewares = [];

    public function __construct(private \FrameworkX\App $app)
    {

    }
    public function add(\Closure|MiddlewareInterface|string $middleware): void
    {
        $this->middlewares[] = $middleware;
    }
}

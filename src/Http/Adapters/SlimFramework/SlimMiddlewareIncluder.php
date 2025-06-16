<?php

namespace Brash\Framework\Http\Adapters\SlimFramework;

use Brash\Framework\Http\Interfaces\MiddlewareIncluderInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slim\App;

class SlimMiddlewareIncluder implements MiddlewareIncluderInterface
{
    public function __construct(private readonly App $app) {}

    public function add(\Closure|MiddlewareInterface|string $middleware): void
    {
        $this->app->add($middleware);
    }
}

<?php

namespace Brash\Framework\Http\Adapters\SlimFramework;

use Brash\Framework\Http\Interfaces\MiddlewareAttachableInterface;
use Psr\Http\Server\MiddlewareInterface;

class SlimRoute implements MiddlewareAttachableInterface
{
    public function __construct(private readonly \Slim\Interfaces\RouteInterface $route) {}

    public function add(MiddlewareInterface|string|callable ...$middleware): void
    {
        foreach ($middleware as $mid) {
            $this->route->add($mid);
        }
    }
}

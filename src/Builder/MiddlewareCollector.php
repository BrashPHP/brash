<?php

namespace Brash\Framework\Builder;

use Brash\Framework\Http\Interfaces\MiddlewareIncluderInterface;
use Psr\Http\Server\MiddlewareInterface as Middleware;

class MiddlewareCollector
{
    public function __construct(private readonly MiddlewareIncluderInterface $root) {}

    /**
     * @var class-string<Middleware>|Middleware
     */
    private array $middlewareClasses = [];

    public function add(Middleware|string $middleware): void
    {
        $this->middlewareClasses[] = $middleware;
    }

    public function collect(): void
    {
        foreach ($this->middlewareClasses as $middlewareClass) {
            $this->root->add($middlewareClass);
        }
    }
}

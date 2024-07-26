<?php

namespace Core\Builder;

use Core\Http\Interfaces\MiddlewareIncluderInterface;
use Psr\Http\Server\MiddlewareInterface as Middleware;


class MiddlewareCollector
{

    public function __construct(private MiddlewareIncluderInterface $root)
    {
    }

    /**
     * @var class-string<Middleware>|Middleware $providers
     */
    private array $middlewareClasses = [];

    public function add(Middleware|string $middleware): void
    {
        $this->middlewareClasses[] = $middleware;
    }

    public function collect()
    {
        foreach ($this->middlewareClasses as $middlewareClass) {
            $this->root->add($middlewareClass);
        }
    }
}

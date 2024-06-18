<?php

namespace Core\Http\Interfaces;

use Psr\Http\Server\MiddlewareInterface;

interface MiddlewareIncluderInterface
{
    function add(\Closure|MiddlewareInterface|string $middleware): void;
}

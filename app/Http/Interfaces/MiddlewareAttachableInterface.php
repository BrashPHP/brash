<?php

namespace Core\Http\Interfaces;

use Psr\Http\Server\MiddlewareInterface;

interface MiddlewareAttachableInterface
{
    public function add(MiddlewareInterface|string|callable ...$middleware): void;
}

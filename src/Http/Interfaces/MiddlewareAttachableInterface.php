<?php

namespace Brash\Framework\Http\Interfaces;

use Psr\Http\Server\MiddlewareInterface;

interface MiddlewareAttachableInterface
{
    public function add(MiddlewareInterface|string|callable ...$middleware): void;
}

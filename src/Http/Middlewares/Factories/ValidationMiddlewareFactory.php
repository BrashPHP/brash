<?php

namespace Brash\Framework\Http\Middlewares\Factories;

use Brash\Framework\Http\Middlewares\ValidationMiddleware;
use Psr\Container\ContainerInterface;

class ValidationMiddlewareFactory
{
    public function __construct(private readonly ContainerInterface $containerInterface) {}

    public function make(object|string $controller): ValidationMiddleware
    {
        return new ValidationMiddleware($this->containerInterface->get($controller));
    }
}

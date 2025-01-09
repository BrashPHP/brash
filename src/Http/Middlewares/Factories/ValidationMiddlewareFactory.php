<?php

namespace Brash\Framework\Http\Middlewares\Factories;

use Brash\Framework\Http\Middlewares\ValidationMiddleware;
use Psr\Container\ContainerInterface;

class ValidationMiddlewareFactory
{
    public function __construct(private ContainerInterface $containerInterface) {}

    public function make(object|string $controller): ValidationMiddleware
    {
        return new ValidationMiddleware($this->containerInterface->get($controller));
    }
}

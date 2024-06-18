<?php
namespace Core\Http\Middlewares\Factories;

use Core\Http\Middlewares\ValidationMiddleware;
use Psr\Container\ContainerInterface;



class ValidationMiddlewareFactory
{
    public function __construct(private ContainerInterface $containerInterface) {
    }
    public function make(object|string $controller): ValidationMiddleware
    {
        return new ValidationMiddleware($this->containerInterface->get($controller));
    }
}

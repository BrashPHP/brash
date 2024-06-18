<?php

namespace Core\Http\Domain;

use Core\Http\Interfaces\ActionInterface;

readonly class RouteModel
{
    public function __construct(
        public array $methods,
        public string $path,
        public array $middlewares,
        public ActionInterface|string $controller
    ) {
    }
}

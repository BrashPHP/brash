<?php

namespace Brash\Framework\Http\Domain;

use Brash\Framework\Http\Interfaces\ActionInterface;

readonly class RouteModel
{
    public function __construct(
        public array $methods,
        public string $path,
        public ActionInterface|string $controller,
        /** @param array<string|object> $middlewares */
        public array $middlewares = [],
    ) {}
}

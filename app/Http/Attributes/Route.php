<?php

namespace Core\Http\Attributes;

use Attribute;
use Psr\Http\Server\MiddlewareInterface;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class Route
{
    /**
     * @param string|string[]                                       $method
     * @param string                                                $path
     * @param MiddlewareInterface|string|MiddlewareInterface[]|null $middleware
     * @param RouteGroup|class-string<object>|object|null           $group
     * @param bool                                                  $skip
     */
    public function __construct(
        public string|array $method,
        public string $path,
        public MiddlewareInterface|string|array|null $middleware = null,
        public RouteGroup|string|null $group = null,
        public bool $skip = false,
    ) {
    }
}


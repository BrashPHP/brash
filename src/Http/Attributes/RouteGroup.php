<?php

namespace Brash\Framework\Http\Attributes;

use Attribute;
use Psr\Http\Server\MiddlewareInterface;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class RouteGroup
{
    /**
     * @param  string  $path
     * @param  class-string|self|null  $parent
     * @param  MiddlewareInterface|string|MiddlewareInterface[]|null  $middleware
     */
    public function __construct(
        public string $prefix,
        public string|self|null $parent = null,
        public MiddlewareInterface|string|array|null $middleware = null,
        public bool $skip = false,
    ) {}
}

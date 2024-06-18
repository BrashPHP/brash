<?php

namespace Core\Http\Attributes;

use Attribute;
use Psr\Http\Server\MiddlewareInterface;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class RouteGroup
{
    /**
     * @param string                                                $path
     * @param class-string|self|null                                $parent
     * @param MiddlewareInterface|string|MiddlewareInterface[]|null $middleware
     * @param bool                                                  $skip
     */
    public function __construct(
        public string $prefix,
        public string|self|null $parent = null,
        public MiddlewareInterface|string|array|null $middleware = null,
        public bool $skip = false,
    ) {
    }
}

<?php

namespace Brash\Framework\Http\Attributes\WebSocket;

use Attribute;
use Psr\Http\Server\MiddlewareInterface;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class WsGateway
{
    /**
     * @param  string|string[]  $events
     * @param  MiddlewareInterface|string|MiddlewareInterface[]|null  $middleware
     */
    public function __construct(
        public string|array $events
    ) {}
}

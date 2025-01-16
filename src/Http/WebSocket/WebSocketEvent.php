<?php

declare(strict_types=1);

namespace Brash\Framework\Http\WebSocket;

use Brash\Websocket\Connection\Connection;
use Brash\Websocket\Events\Protocols\Event;

class WebSocketEvent extends Event
{
    public function __construct(
        public string $eventName,
        public mixed $data,
        public Connection $connection,
        public WebSocketGateway $webSocketGateway
    ) {}
}

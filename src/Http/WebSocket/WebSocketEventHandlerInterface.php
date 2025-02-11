<?php

namespace Brash\Framework\Http\WebSocket;

interface WebSocketEventHandlerInterface{
    public function handle(WebSocketEvent $webSocketEvent): void;
}

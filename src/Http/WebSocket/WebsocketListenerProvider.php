<?php

namespace Brash\Framework\Http\WebSocket;

use Brash\Websocket\Events\Protocols\ListenerInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

class WebsocketListenerProvider implements ListenerProviderInterface
{
    /**
     * @var \WeakMap<object,\Brash\Websocket\Events\Protocols\ListenerInterface[]|callable[]>
     */
    private \WeakMap $listeners;

    public function __construct()
    {
        $this->listeners = new \WeakMap;
    }

    public function getListenersForEvent(object|string $event): iterable
    {
        return $this->listeners->offsetExists($event) ? $this->listeners->offsetGet($event) : [];
    }

    public function addEventListener(object|string $event, ListenerInterface|callable $listener): static
    {
        $mappedListeners = $this->listeners->offsetExists($event) ? $this->listeners->offsetGet($event) : [];

        $this->listeners->offsetSet($event, [...$mappedListeners, $listener]);

        return $this;
    }
}

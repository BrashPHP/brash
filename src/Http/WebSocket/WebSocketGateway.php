<?php

namespace Brash\Framework\Http\WebSocket;

use Brash\Websocket\Connection\Connection;
use Brash\Websocket\Events\Protocols\ListenerInterface;
use Brash\Websocket\Exceptions\WebSocketException;
use Brash\Websocket\Frame\Enums\FrameTypeEnum;
use Brash\Websocket\Message\Message;
use Brash\Websocket\Message\Protocols\ConnectionHandlerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Psr\Log\LoggerInterface;

class WebSocketGateway implements ConnectionHandlerInterface
{
    /**
     * @var Collection<Connection>
     */
    private readonly Collection $connections;

    private readonly WebsocketListenerProvider $listenerProvider;

    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
        $this->connections = new ArrayCollection;
        $this->listenerProvider = new WebsocketListenerProvider;
    }

    public function echo(string $message): void
    {
        $connections = $this->connections;
        foreach ($connections as $conn) {
            $conn->writeText($message);
        }
    }

    public function listenTo(object|string $event, ListenerInterface|callable $listener): void
    {
        $this->listenerProvider->addEventListener($event, $listener);
    }

    public function getConnections(): Collection
    {
        return $this->connections;
    }

    public function broadcast(Connection $connection, string $message): void
    {
        foreach ($this->connections as $conn) {
            if ($connection === $conn) {
                continue;
            }

            $conn->writeText($message);
        }
    }

    #[\Override]
    public function hasSupport(Message $message): bool
    {
        return $message->getOpcode() === FrameTypeEnum::Text;
    }

    #[\Override]
    public function handle(Message $message, Connection $connection): void
    {
        $messageContent = $message->getContent();

        $rawValues = json_validate($messageContent) ?
            json_decode($messageContent, associative: true) :
            ['data' => $messageContent];

        $event = array_key_exists('event_name', $rawValues) ? $rawValues['event_name'] : '';
        $listeners = $this->listenerProvider->getListenersForEvent($event);
        $data = $rawValues['data'] ?? [];

        $eventObject = new WebSocketEvent($event, $data, $connection, $this);

        foreach ($listeners as $listener) {
            if (is_callable($listener)) {
                $listener($eventObject);
            } elseif ($listener instanceof ListenerInterface) {
                $listener->execute($eventObject);
            }
        }
    }

    #[\Override]
    public function onDisconnect(Connection $connection): void
    {
        $this->connections->add($connection);
    }

    #[\Override]
    public function onOpen(Connection $connection): void
    {
        $this->connections->removeElement($connection);
    }

    #[\Override]
    public function onError(WebSocketException $e, Connection $connection): void
    {
        $this->logger->error('An error occured within the websocket handler', [
            'exception' => $e,
            'connection' => $connection,
        ]);
    }
}

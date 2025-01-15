<?php

namespace Brash\Framework\Http\WebSocket;

use Brash\Websocket\Connection\Connection;
use Brash\Websocket\Events\Protocols\Event;
use Brash\Websocket\Events\Protocols\ListenerInterface;
use Brash\Websocket\Exceptions\WebSocketException;
use Brash\Websocket\Frame\Enums\FrameTypeEnum;
use Brash\Websocket\Message\Message;
use Brash\Websocket\Message\Protocols\ConnectionHandlerInterface;
use Psr\Log\LoggerInterface;

class WebSocketGateway implements ConnectionHandlerInterface
{
    private \SplObjectStorage $connections;

    private WebsocketListenerProvider $listenerProvider;

    public function __construct(
        private LoggerInterface $logger,
    ) {
        $this->connections = new \SplObjectStorage;
        $this->listenerProvider = new WebsocketListenerProvider;
    }

    public function echo(string $message): void
    {
        /**
         * @var Connection[]
         */
        $connections = $this->connections;
        foreach ($connections as $conn) {
            $conn->writeText($message);
        }
    }

    public function listenTo(object|string $event, ListenerInterface|callable $listener): void
    {
        $this->listenerProvider->addEventListener($event, $listener);
    }

    public function broadcast(string $message, Connection $connection): void
    {
        /**
         * @var Connection[]
         */
        $connections = $this->connections;
        foreach ($connections as $conn) {
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
        if (json_validate($messageContent)) {
            $rawValues = json_decode($messageContent, associative: true);
            if (array_key_exists('event_name', $rawValues)) {
                $listeners = $this->listenerProvider->getListenersForEvent($rawValues['event_name']);
                $eventObject = new class($rawValues['event_name'], $rawValues['data'] ?? [], $connection, $this) extends Event
                {
                    public function __construct(
                        public string $eventName,
                        public mixed $data,
                        public Connection $connection,
                        public WebSocketGateway $webSocketGateway
                    ) {}
                };

                foreach ($listeners as $listener) {
                    if (is_callable($listener)) {
                        $listener($eventObject);
                    } elseif ($listener instanceof ListenerInterface) {
                        $listener->execute($eventObject);
                    }
                }
            }
        }
    }

    #[\Override]
    public function onDisconnect(Connection $connection): void
    {
        $this->connections->detach($connection);
    }

    #[\Override]
    public function onOpen(Connection $connection): void
    {
        $this->connections->attach($connection);
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

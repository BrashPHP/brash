<?php

namespace Brash\Framework\Http\WebSocket;

use Brash\Websocket\Connection\Connection;
use Brash\Websocket\Exceptions\WebSocketException;
use Brash\Websocket\Frame\Enums\FrameTypeEnum;
use Brash\Websocket\Message\Message;
use Brash\Websocket\Message\Protocols\AbstractBinaryMessageHandler;
use Brash\Websocket\Message\Protocols\AbstractTextMessageHandler;
use Brash\Websocket\Message\Protocols\ConnectionHandlerInterface;
use Psr\Log\LoggerInterface;

final class WebSocketGateway implements ConnectionHandlerInterface
{
    public function __construct(private LoggerInterface $loggerInterface)
    {

    }

    #[\Override]
    public function hasSupport(Message $message): bool
    {
        return $message->getOpcode() === FrameTypeEnum::Text;
    }

    #[\Override]
    public function handle(Message $message, Connection $connection): void
    {
        // $this->handleTextData($message->getContent(), $connection);
        dump($message->getContent());
    }

    #[\Override]
    public function onDisconnect(Connection $connection): void
    {
        $connection->writeText('Disconnected');
        $connection->getLogger()->info("New Connection removed!");

    }

    #[\Override]
    public function onOpen(Connection $connection): void
    {
        $connection->getLogger()->info("New Connection added!");
    }

    #[\Override]
    public function onError(WebSocketException $e, Connection $connection): void
    {

    }
}

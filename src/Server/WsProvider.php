<?php

namespace Brash\Framework\Server;

use Brash\Framework\Http\WebSocket\WebSocketGateway;
use Brash\Websocket\Config\Config;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;

final class WsProvider
{
    public function provide(
        LoopInterface $loopInterface,
        ContainerInterface $containerInterface,

    ): ?\Brash\WebSocketMiddleware\WebSocketMiddleware {
        if (class_exists(\Brash\WebSocketMiddleware\MiddlewareFactory::class)) {
            $factory = new \Brash\WebSocketMiddleware\MiddlewareFactory();
            $loggerInterface = $containerInterface->get(LoggerInterface::class);
            $config = $containerInterface->has('middleware') ? $containerInterface->get('middleware') : [];
            $paths = ['/ws'];
            $objectConfig = new Config();
            $factory = $factory->withLoop($loopInterface)->withLogger($loggerInterface);
            if ($config !== []) {
                $paths = key_exists('paths', $config) ?
                    array_merge((array) $config['paths'], $paths) :
                    $paths;
                $objectConfig = Config::createFromArray($config);
            }

            $factory = $factory->withPaths($paths)->withConfig($objectConfig);

            return $factory->create(
                connectionHandler: new WebSocketGateway($loggerInterface)
            );
        }

        return null;
    }
}

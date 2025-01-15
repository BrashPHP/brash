<?php

namespace Brash\Framework\Server;

use Brash\Framework\Http\WebSocket\WebSocketGateway;
use Brash\Websocket\Config\Config;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;

final class WsProvider
{
    public function provide(
        ContainerInterface $containerInterface,
        ?LoopInterface $loopInterface = null,
    ): ?\Brash\WebSocketMiddleware\WebSocketMiddleware {
        $loopInterface ??= Loop::get();
        if (class_exists(\Brash\WebSocketMiddleware\MiddlewareFactory::class)) {
            $factory = new \Brash\WebSocketMiddleware\MiddlewareFactory;
            $loggerInterface = $containerInterface->get(LoggerInterface::class);
            $config = $containerInterface->has('middleware') ? $containerInterface->get('middleware') : [];
            $paths = ['/ws'];
            $objectConfig = new Config;
            $factory = $factory->withLoop($loopInterface)->withLogger($loggerInterface);
            if ($config !== []) {
                $paths = array_key_exists('paths', $config) ?
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

<?php

namespace Brash\Framework\Http\WebSocket;

use Brash\Framework\Http\Attributes\WebSocket\WsGateway;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use Spatie\StructureDiscoverer\Discover;

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
            $config = $containerInterface->has('websocket') ? $containerInterface->get('websocket') : [];
            $paths = ['/ws'];
            $objectConfig = new \Brash\Websocket\Config\Config;
            $factory = $factory->withLoop($loopInterface)->withLogger($loggerInterface);
            $actionsPath = $config['websocket_handlers'] ?? getcwd();
            if ($config !== []) {
                $paths = array_key_exists('paths', $config) ?
                    array_merge((array) $config['paths'], $paths) :
                    $paths;
                $objectConfig = \Brash\Websocket\Config\Config::createFromArray($config);
            }

            $factory = $factory->withPaths($paths)->withConfig($objectConfig);
            $webSocketGateway = new WebSocketGateway($loggerInterface);
            $this->subscribeListeners($actionsPath, $webSocketGateway, $containerInterface);

            return $factory->create(
                $webSocketGateway
            );
        }

        return null;
    }

    public function subscribeListeners(
        string $path,
        WebSocketGateway $webSocketGateway,
        ContainerInterface $containerInterface
    ): void {
        $strSubscribers = Discover::in(directories: $path)->classes()->withAttribute(WsGateway::class)->get();

        foreach ($strSubscribers as $strSubscriber) {
            $reflection = new \ReflectionClass($strSubscriber);

            $attributes = $reflection->getAttributes(WsGateway::class);
            /** @var \ReflectionAttribute */
            $attribute = array_pop($attributes);
            if ($attribute !== null && $reflection->implementsInterface(WebSocketEventHandlerInterface::class)) {
                /** @var WebSocketEventHandlerInterface */
                $action = $containerInterface->get($reflection->getName());
                /** @var WsGateway */
                $objectAttribute = $attribute->newInstance();
                $events = (array) $objectAttribute->events;
                foreach ($events as $event) {
                    $webSocketGateway->listenTo($event, $action->handle(...));
                }
            }
        }
    }
}

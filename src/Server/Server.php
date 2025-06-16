<?php

declare(strict_types=1);

namespace Brash\Framework\Server;

use Brash\Framework\Http\Interfaces\ApplicationInterface;
use Brash\Framework\Http\Middlewares\FiberMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Socket\SocketServer;

use function React\Async\async;

final class Server
{
    private readonly LoopInterface $loop;

    /**
     * @var callable[]
     */
    private array $handlers = [];

    public function __construct(
        private readonly ApplicationInterface $app,
        ?LoopInterface $loop = null,
        private readonly string $address = '0.0.0.0',
        private readonly int $port = 8080,
    ) {
        $this->loop = $loop ?? Loop::get();
    }

    public function addHandler(callable $handler): static
    {
        $this->handlers[] = $handler;

        return $this;
    }

    public function run(): void
    {
        $serverAddress = sprintf('%s:%d', $this->address, $this->port);
        $fiberMiddleware = new FiberMiddleware;
        $httpHandler = $this->createAsyncHandler();

        $handlers = [$fiberMiddleware, ...$this->handlers, $httpHandler];

        $http = new \React\Http\HttpServer(
            ...$handlers
        );

        echo 'Server running at '.$serverAddress.PHP_EOL;

        $socket = new SocketServer($serverAddress, loop: $this->loop);

        $http->listen($socket);

        echo 'Listening on '.str_replace('tcp:', 'http:', $socket->getAddress()).PHP_EOL;

        $this->loop->run();
    }

    public function close(): void
    {
        $this->loop->stop();
    }

    private function createAsyncHandler(): callable
    {
        return async(
            fn (ServerRequestInterface $request): ResponseInterface => $this->app->handle($request)
        );
    }
}

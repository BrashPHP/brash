<?php

declare(strict_types=1);

namespace Brash\Framework\Server;

use Brash\Framework\Http\Interfaces\ApplicationInterface;
use Brash\Framework\Http\Middlewares\FiberMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Socket\SocketServer;

use function React\Async\async;

final class Server
{
    private LoopInterface $loop;

    public function __construct(
        private ApplicationInterface $app,
        ?LoopInterface $loop = null,
        private string $address = '0.0.0.0',
        private int $port = 8080,
    ) {
        $this->loop = $loop ?? Loop::get();
    }

    public function run()
    {
        $serverAddress = sprintf('%s:%d', $this->address, $this->port);

        $http = new \React\Http\HttpServer(
            // new \React\Http\Middleware\StreamingRequestMiddleware(),
            new FiberMiddleware,
            $this->createAsyncHandler(),
        );

        echo 'Server running at '.$serverAddress.PHP_EOL;

        $socket = new SocketServer($serverAddress, loop: $this->loop);

        $http->listen($socket);

        echo 'Listening on '.str_replace('tcp:', 'http:', $socket->getAddress()).PHP_EOL;

        $this->loop->run();
    }

    public function close()
    {
        $this->loop->stop();
    }

    private function createAsyncHandler()
    {
        return async(
            function (ServerRequestInterface $request) {
                return $this->app->handle($request);
            }
        );
    }
}

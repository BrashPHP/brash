<?php

declare(strict_types=1);

namespace Core\Server;

use Core\Http\Factories\ContainerFactory;
use Core\Http\Middlewares\FiberMiddleware;
use React\EventLoop\LoopInterface;
use Core\Builder\AppBuilderManager;
use React\EventLoop\Loop;
use Slim\App;
use function Core\functions\isProd;
use function React\Async\async;

final class Server
{
    private LoopInterface $loop;
    private App $app;

    public function __construct(
        ?LoopInterface $loop = null,
        private string $address = '0.0.0.0',
        private int $port = 8080
    ) {
        $this->loop = $loop ?? Loop::get();
        $containerFactory = new ContainerFactory(enableCompilation: isProd());
        $appBuilder = new AppBuilderManager($containerFactory->get());
        $appBuilder->useDefaultShutdownHandler(true);
        $this->app = $appBuilder->build();
    }

    public function run()
    {
        $serverAddress = "{$this->address}:{$this->port}";

        $http = new \React\Http\HttpServer(
            // new \React\Http\Middleware\StreamingRequestMiddleware(),
            new FiberMiddleware(),
            $this->createAsyncHandler(),
        );

        echo "Server running at $serverAddress" . PHP_EOL;

        $socket = new \React\Socket\SocketServer($serverAddress, loop: $this->loop);

        $http->listen($socket);

        echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;

        $this->loop->run();
    }

    private function createAsyncHandler()
    {
        return async(
            function (\Psr\Http\Message\ServerRequestInterface $request) {
                try {
                    return $this->app->handle($request);
                } catch (\Throwable $th) {
                    dd($th);
                }
            }
        );
    }
}

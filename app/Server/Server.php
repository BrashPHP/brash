<?php

declare(strict_types=1);

namespace Core\Server;

use React\EventLoop\LoopInterface;
use Core\Builder\AppBuilderManager;
use Core\Builder\Factories\ContainerFactory;
use React\EventLoop\Loop;
use function Core\functions\isProd;
use function React\Async\async;

final class Server
{
    private LoopInterface $loop;

    public function __construct(
        ?LoopInterface $loop = null,
        private string $address = '0.0.0.0',
        private int $port = 8080
    ) {
        $this->loop = $loop ?? Loop::get();
    }

    public function run()
    {
        $serverAddress = "{$this->address}:{$this->port}";
        $http = new \React\Http\HttpServer(
            // new React\Http\Middleware\StreamingRequestMiddleware(),
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
        return async(function (\Psr\Http\Message\ServerRequestInterface $request) {
            try {

                $containerFactory = new ContainerFactory(enableCompilation: isProd());

                $appBuilder = new AppBuilderManager($containerFactory->get());
                $appBuilder->useDefaultShutdownHandler(true);
                // $requestFactory = new RequestFactory();
                // $request = $requestFactory->createRequest();

                $app = $appBuilder->build($request);
                // Run App & Emit Response
                $response = $app->handle($request);
                // $responseEmitter = new SlimResponseEmitter();
                // $responseEmitter->emit($response);

                return $response;
            } catch (\Throwable $th) {
                echo $th;
            }
        });
    }
}

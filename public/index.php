<?php

declare(strict_types=1);

use React\EventLoop\Loop;
use Revolt\EventLoop\React\Internal\EventLoopAdapter;

require_once __DIR__ . '/../vendor/autoload.php';


try {
    Loop::set(EventLoopAdapter::get());

    $loop = Loop::get();

    ini_set('memory_limit', '512M');
    $handler = require_once __DIR__ . '/react.php';

    $http = new React\Http\HttpServer(
        // new React\Http\Middleware\StreamingRequestMiddleware(),
        $handler()
    );
    
    $serverAddress = '0.0.0.0:8080';
    
    echo "Server running at $serverAddress" . PHP_EOL;
    
    $socket = new React\Socket\SocketServer($serverAddress, loop: $loop);
    
    $http->listen($socket);
    
    $loop->run();
    
    echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;
    
} catch (\Throwable $th) {
    echo $th;
}

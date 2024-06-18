<?php

declare(strict_types=1);

use Core\Server\Server;
use React\EventLoop\Loop;
use Revolt\EventLoop\React\Internal\EventLoopAdapter;

require_once __DIR__ . '/../vendor/autoload.php';


try {
    Loop::set(EventLoopAdapter::get());

    $server = new Server();
    
    $server->run();
} catch (\Throwable $th) {
    echo $th;
}

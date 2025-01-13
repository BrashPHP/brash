<?php

declare(strict_types=1);

use Brash\Framework\Builder\AppBuilderManager;
use Brash\Framework\Http\Factories\ContainerFactory;
use Brash\Framework\Server\Server;
use React\EventLoop\Loop;
use Revolt\EventLoop\React\Internal\EventLoopAdapter;
use function Brash\Framework\functions\isProd;


require_once __DIR__ . '/../../vendor/autoload.php';

echo "Started";
Loop::set(EventLoopAdapter::get());

$containerFactory = new ContainerFactory(enableCompilation: isProd());

$appBuilder = new AppBuilderManager($containerFactory->get());
$appBuilder->useDefaultShutdownHandler(true);
$app = $appBuilder->build();

$server = new Server($app);

$server->run();

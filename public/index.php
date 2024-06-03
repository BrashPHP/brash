<?php

declare(strict_types=1);

use Core\Builder\AppBuilderManager;
use Core\Builder\Factories\ContainerFactory;
use Core\Http\Factories\RequestFactory;
use Slim\ResponseEmitter as SlimResponseEmitter;

use function Core\functions\isProd;

require __DIR__ . '/../vendor/autoload.php';


$containerFactory = new ContainerFactory(enableCompilation: isProd());

$appBuilder = new AppBuilderManager($containerFactory->get());
$appBuilder->useDefaultShutdownHandler(true);
$requestFactory = new RequestFactory();
$request = $requestFactory->createRequest();

$app = $appBuilder->build($request);
// Run App & Emit Response
$response = $app->handle($request);
$responseEmitter = new SlimResponseEmitter();
$responseEmitter->emit($response);


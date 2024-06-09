<?php

declare(strict_types=1);

use Core\Builder\AppBuilderManager;
use Core\Builder\Factories\ContainerFactory;
use Core\Http\Factories\RequestFactory;
use Slim\ResponseEmitter as SlimResponseEmitter;

use function Core\functions\isProd;

require_once __DIR__ . '/../vendor/autoload.php';


$containerFactory = new ContainerFactory(enableCompilation: isProd());

$container = $containerFactory->get();

$appBuilder = new AppBuilderManager($container);
$appBuilder->useDefaultShutdownHandler(true);
$requestFactory = new RequestFactory();
$request = $requestFactory->createRequest();

$app = $appBuilder->build($request);
// Run App & Emit Response
$response = $app->handle($request);
$responseEmitter = new SlimResponseEmitter();
$responseEmitter->emit($response);


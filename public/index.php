<?php

declare(strict_types=1);

use Core\Builder\AppBuilderManager;
use Core\Builder\Factories\ContainerFactory;
use Core\Http\Factories\RequestFactory;
use Slim\ResponseEmitter as SlimResponseEmitter;
use Symfony\Component\Dotenv\Dotenv;

use function Core\functions\isProd;

require __DIR__ . '/../vendor/autoload.php';

$envPath = __DIR__ . '/../.env';

if (file_exists($envPath)) {
    $dotenv = new Dotenv();
    $dotenv->load($envPath);
}


$containerFactory = new ContainerFactory();
$containerFactory
    // Set to true in production
    ->enableCompilation(isProd());

$appBuilder = new AppBuilderManager($containerFactory->get());
$appBuilder->useDefaultShutdownHandler(true);
$requestFactory = new RequestFactory();
$request = $requestFactory->createRequest();

$app = $appBuilder->build($request);
// Run App & Emit Response
$response = $app->handle($request);
$responseEmitter = new SlimResponseEmitter();
$responseEmitter->emit($response);


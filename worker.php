<?php

declare(strict_types=1);

use Core\Builder\AppBuilderManager;
use Core\Http\Factories\ContainerFactory;
use Core\Http\Factories\RequestFactory;

require_once __DIR__ . '/vendor/autoload.php';

$containerFactory = new ContainerFactory();

$container = $containerFactory->get();

$appBuilder = new AppBuilderManager($container);

$requestFactory = new RequestFactory();

$request = $requestFactory->createRequest();

$app = $appBuilder->build();

/** @var Spiral\RoadRunner\Http\PSR7WorkerInterface $psr7Worker */
$psr7Worker = $app->getContainer()->get(Spiral\RoadRunner\Http\PSR7WorkerInterface::class);


while ($req = $psr7Worker->waitRequest()) {
    try {
        $res = $app->handle($req);
        $psr7Worker->respond($res);
    } catch (Throwable $e) {
        $psr7Worker->getWorker()->error((string) $e);
    }
}

<?php

use Core\Builder\AppBuilderManager;
use Core\Builder\Factories\ContainerFactory;
use function Core\functions\isProd;
use function React\Async\async;

return function () {
    return async(function (Psr\Http\Message\ServerRequestInterface $request) {
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
};
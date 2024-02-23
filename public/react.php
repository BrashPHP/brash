<?php

use Core\Builder\AppBuilderManager;
use Core\Builder\Factories\ContainerFactory;
use function Core\functions\isProd;
use function React\Async\async;

return function () {
    return async(function (Psr\Http\Message\ServerRequestInterface $request) {
        try {
            $containerFactory = new ContainerFactory();

            $containerFactory
                // Set to true in production
                ->enableCompilation(isProd())
            ;

            $appBuilder = new AppBuilderManager($containerFactory->get());

            $app = $appBuilder->build($request);

            // Run App & Emit Response
            $response = $app->handle($request);

            return $response;
        } catch (\Throwable $th) {
            echo $th;
        }
    });
};
<?php

declare(strict_types=1);

namespace Core\Providers;

use DI\ContainerBuilder;
use Core\Providers\AppProviderInterface;
use Spiral\RoadRunner\Http\PSR7WorkerInterface;
use function DI\autowire;


class WorkerProvider implements AppProviderInterface
{
    public function provide(ContainerBuilder $container)
    {
        if (boolval(getenv("RR"))) {
            $psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();
            $container->addDefinitions(
                [
                    \Psr\Http\Message\ResponseFactoryInterface::class => $psr17Factory,
                    \Psr\Http\Message\ServerRequestFactoryInterface::class => $psr17Factory,
                    \Psr\Http\Message\StreamFactoryInterface::class => $psr17Factory,
                    \Psr\Http\Message\UploadedFileFactoryInterface::class => $psr17Factory,
                    \Spiral\RoadRunner\WorkerInterface::class => fn() => \Spiral\RoadRunner\Worker::create(),
                    PSR7WorkerInterface::class => autowire(\Spiral\RoadRunner\Http\PSR7Worker::class),
                ]
            );
        }
    }
}

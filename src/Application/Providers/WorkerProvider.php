<?php

declare(strict_types=1);

namespace App\Application\Providers;

use DI\ContainerBuilder;
use Core\Providers\AppProviderInterface;
use Spiral\RoadRunner\Http\PSR7WorkerInterface;
use function DI\autowire;


class WorkerProvider implements AppProviderInterface
{
    /**
     * Summary of provider
     * @param \DI\ContainerBuilder $container
     * @return void
     */
    public function provide(ContainerBuilder $container)
    {
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

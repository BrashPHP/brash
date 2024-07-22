<?php

namespace Core\Worker;

use App\Application\Providers\WorkerProvider;
use Core\Builder\AppBuilderManager;
use Core\Http\Factories\ContainerFactory;
use Spiral\RoadRunner\Http\PSR7WorkerInterface;


final class RoadRunnerWorker
{
    public static function run() : void{
        $containerFactory = new ContainerFactory();
        $containerFactory->addProviders(WorkerProvider::class);
        $container = $containerFactory->get();
        $appBuilder = new AppBuilderManager($container);
        
        $app = $appBuilder->build();
        
        /** @var PSR7WorkerInterface $psr7Worker */
        $psr7Worker = $container->get(PSR7WorkerInterface::class);
        
        while ($req = $psr7Worker->waitRequest()) {
            try {
                $res = $app->handle($req);
                $psr7Worker->respond($res);
            } catch (\Throwable $e) {
                $psr7Worker->getWorker()->error((string) $e);
            }
        }
    }
}



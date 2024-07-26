<?php


use Core\Builder\ProvidersCollector;

use Core\Providers\AppProviderInterface;
use DI\ContainerBuilder;


class ProviderStubClass implements AppProviderInterface{
    public function provide(ContainerBuilder $container){
        $container->addDefinitions(['a'=>'b']);
    }
}

test('should collect Providers in correct oder and call them with correct values', function () {
    $providersCollector = new ProvidersCollector();
    $testStubProviders1 = new class implements AppProviderInterface{
        public function provide(ContainerBuilder $container){
            $container->addDefinitions(['a'=>'b']);
        }
    };
    
    $testStubProviders2 = new class implements AppProviderInterface{
        public function provide(ContainerBuilder $container){
            $container->addDefinitions(['c'=>'d']);
        }
    };
    /** @var ContainerBuilder|\Mockery\MockInterface */
    $mockContainerBuilder = spy(ContainerBuilder::class);
    $providersCollector->addProvider($testStubProviders1);
    $providersCollector->addProvider($testStubProviders2);
    $providersCollector->addProvider(ProviderStubClass::class);

    $providersCollector->execute($mockContainerBuilder);
    $mockContainerBuilder->shouldHaveReceived("addDefinitions", [['a'=>'b']])->twice();
    $mockContainerBuilder->shouldHaveReceived("addDefinitions", [['c'=>'d']])->once();
});

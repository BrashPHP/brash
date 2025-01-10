<?php

declare(strict_types=1);

use Brash\Framework\Http\Factories\ContainerFactory;
use Brash\Framework\Providers\AppProviderInterface;
use Doctrine\ORM\EntityManagerInterface as EntityManager;

use function PHPUnit\Framework\assertArrayHasKey;

test('if setup container works', function () {
    $containerFactory = new ContainerFactory;
    $containerFactory->addProviders(new class implements AppProviderInterface
    {
        public function provide(DI\ContainerBuilder $container): void
        {
            $src = __DIR__;
            $container->addDefinitions([
                'doctrine_metadata_dirs' => [$src.'/Models'],
            ]);
        }
    });

    $container = $this->getContainer();
    $doctrine = $container->get('doctrine');

    assertArrayHasKey('connection', $doctrine);
});

test('if entity manager is not null', function () {
    $container = $this->getContainer();
    $em = $container->get(EntityManager::class);

    expect($em)->toBeObject();
});

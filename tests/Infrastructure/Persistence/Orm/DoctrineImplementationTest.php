<?php

declare(strict_types=1);

use Brash\Framework\Data\BehaviourComponents\DatabaseCreator;
use Brash\Framework\Http\Factories\ContainerFactory;
use Brash\Framework\Providers\AppProviderInterface;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Tests\Infrastructure\Persistence\Orm\Models\UserModel;

test('if setup container works', function (): void {
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

    expect($doctrine)->toHaveKey('connection');
});

test('if entity manager is not null', function (): void {
    $container = $this->getContainer();
    $em = $container->get(EntityManager::class);

    expect($em)->toBeObject();
});

test('should get last inserted entity', function (): void {
    $containerFactory = new ContainerFactory;
    $containerFactory->addProviders(new class implements AppProviderInterface
    {
        public function provide(DI\ContainerBuilder $container): void
        {
            $src = __DIR__;
            $container->addDefinitions([
                'doctrine_metadata_dirs' => [implode(array: [$src, 'Models'], separator: DIRECTORY_SEPARATOR)],
            ]);
        }
    });

    $container = $containerFactory->get();
    DatabaseCreator::createDoctrineDatabase($container);
    /** @var EntityManager */
    $em = $container->get(EntityManager::class);
    $user = new UserModel;
    $user->setName('Gabo');

    $em->persist($user);
    $em->flush();

    expect($user->getId())->toBeInt();
    expect($user->getId())->toBeGreaterThan(0);

    $em->close();
});

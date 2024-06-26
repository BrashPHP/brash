<?php

namespace Core\Data\OrmFactories;

use Core\Data\Doctrine\EntityManagerBuilder;
use Psr\Container\ContainerInterface;

use Doctrine\ORM\EntityManagerInterface;
use Core\Decorators\ReopeningEntityManagerDecorator;
use Core\Data\Doctrine\ManagerRegistry;
use Psr\Log\LoggerInterface;


final class DoctrineOrmFactory
{
    public function create(): array
    {
        return [
            ManagerRegistry::class => static fn(ContainerInterface $container) =>
                new ManagerRegistry($container->get('settings')['doctrine'], $container->get(LoggerInterface::class)),

            ReopeningEntityManagerDecorator::class => static fn(
                ContainerInterface $container
            ) => new ReopeningEntityManagerDecorator($container),

            EntityManagerInterface::class => static fn(
                ContainerInterface $container
            ) => EntityManagerBuilder::produce($container->get('settings')['doctrine'])
        ];
    }
}

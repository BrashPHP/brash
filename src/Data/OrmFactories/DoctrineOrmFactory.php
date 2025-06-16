<?php

namespace Brash\Framework\Data\OrmFactories;

use Brash\Framework\Data\Doctrine\ManagerRegistry;
use Brash\Framework\Decorators\ReopeningEntityManagerDecorator;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

final class DoctrineOrmFactory
{
    public function create(): array
    {
        return [
            ManagerRegistry::class => static fn (ContainerInterface $container): \Brash\Framework\Data\Doctrine\ManagerRegistry => new ManagerRegistry($container->get('doctrine'), $container->get(LoggerInterface::class)),

            ReopeningEntityManagerDecorator::class => static fn (
                ContainerInterface $container
            ): \Brash\Framework\Decorators\ReopeningEntityManagerDecorator => new ReopeningEntityManagerDecorator($container),

            EntityManagerInterface::class => static fn (
                ContainerInterface $container
            ) => $container->get(ManagerRegistry::class)->getManager(),
        ];
    }
}

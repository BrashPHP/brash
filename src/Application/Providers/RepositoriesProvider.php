<?php

declare(strict_types=1);

namespace App\Application\Providers;

use App\Domain\Repositories\AccountRepository;
use App\Domain\Repositories\MarkerRepositoryInterface;
use App\Domain\Repositories\MuseumRepository;
use App\Domain\Repositories\SignatureTokenRepositoryInterface;
use App\Domain\Repositories\SignatureTokenRetrieverInterface;
use App\Domain\Repositories\UserRepository;
use App\Infrastructure\Persistence\Doctrine\DoctrineAccountRepository;
use App\Infrastructure\Persistence\Doctrine\MarkerDoctrineRepository;
use App\Infrastructure\Persistence\Doctrine\MuseumDoctrineRepository;
use App\Infrastructure\Persistence\Doctrine\SignatureTokenRepository;
use App\Infrastructure\Persistence\MemoryRepositories\InMemoryUserRepository;
use Core\Providers\AppProviderInterface;
use DI\ContainerBuilder;

use function DI\autowire;

class RepositoriesProvider implements AppProviderInterface
{
    public function provide(ContainerBuilder $container): void
    {
        // Here we map our UserRepository interface to its in memory implementation
        $container->addDefinitions(
            array_map(
                autowire(...),
                $this->createRepositoriesDefinitions()
            )
        );
    }

    private function createRepositoriesDefinitions(): array
    {
        return [
            UserRepository::class => InMemoryUserRepository::class,
            AccountRepository::class => DoctrineAccountRepository::class,
            MuseumRepository::class => MuseumDoctrineRepository::class,
            MarkerRepositoryInterface::class => MarkerDoctrineRepository::class,
            SignatureTokenRepositoryInterface::class => SignatureTokenRepository::class,
            SignatureTokenRetrieverInterface::class => SignatureTokenRepository::class,
        ];
    }
}

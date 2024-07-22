<?php

declare(strict_types=1);

namespace App\Application\Providers;

use function DI\autowire;
use DI\ContainerBuilder;
use App\Domain\Repositories\AccountRepository;
use App\Domain\Repositories\MarkerRepositoryInterface;
use App\Domain\Repositories\MuseumRepository;
use App\Domain\Repositories\SignatureTokenRepositoryInterface;
use App\Domain\Repositories\SignatureTokenRetrieverInterface;
use App\Domain\Repositories\UserRepository;
use App\Infrastructure\Persistence\Cycle\CycleAccountRepository;
use App\Infrastructure\Persistence\Doctrine\DoctrineAccountRepository;
use App\Infrastructure\Persistence\Doctrine\MarkerDoctrineRepository;
use App\Infrastructure\Persistence\Doctrine\MuseumDoctrineRepository;
use App\Infrastructure\Persistence\Doctrine\SignatureTokenRepository;
use App\Infrastructure\Persistence\MemoryRepositories\{
    InMemoryUserRepository,
    // InMemoryAccountRepository,
    InMemoryMarkerRepository,
    InMemoryMuseumRepository,
    InMemorySignatureTokenRepository
};
use Core\Providers\AppProviderInterface;

class RepositoriesProvider implements AppProviderInterface
{
    public function provide(ContainerBuilder $container)
    {
        // Here we map our UserRepository interface to its in memory implementation
        $container->addDefinitions(
            array_map(
                autowire(...),
                $this->createRepositoriesDefinitions()
            )
        );
    }

    private function createRepositoriesDefinitions()
    {
        if (boolval(getenv("RR"))) {
            return [
                UserRepository::class => InMemoryUserRepository::class,
                AccountRepository::class => CycleAccountRepository::class,
                MarkerRepositoryInterface::class => InMemoryMarkerRepository::class,
                MuseumRepository::class => InMemoryMuseumRepository::class,
                SignatureTokenRepositoryInterface::class => InMemorySignatureTokenRepository::class,
                SignatureTokenRetrieverInterface::class => InMemorySignatureTokenRepository::class
            ];
        }

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

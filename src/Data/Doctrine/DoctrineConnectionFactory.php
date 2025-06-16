<?php

namespace Brash\Framework\Data\Doctrine;

use Brash\Dbal\DriverManager;
use Brash\Framework\Data\Domain\ConnectionModel;
use Doctrine\DBAL\Tools\DsnParser;
use Doctrine\DBAL\Types\Type;

final class DoctrineConnectionFactory
{
    public function createConnectionFromArray(array $doctrine, \Doctrine\ORM\Configuration $config): \Doctrine\DBAL\Connection
    {
        if (! Type::hasType('uuid')) {
            Type::addType('uuid', \Ramsey\Uuid\Doctrine\UuidType::class);
        }

        $connection = $doctrine['connection'];

        assert($connection instanceof ConnectionModel);

        $connectionParams = $connection->url !== '' ? $this->getFromUrl($connection->url) : $connection->getAsArray();

        return DriverManager::getConnection(
            $connectionParams,
            $config
        );
    }

    private function getFromUrl(string $url): array
    {
        $dsnParser = new DsnParser(['mysql' => 'mysqli', 'postgresql' => 'pdo_pgsql']);

        return $dsnParser->parse($url);
    }
}

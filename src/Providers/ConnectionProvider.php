<?php

namespace Brash\Framework\Providers;

use Brash\Framework\Data\Domain\ConnectionModel;
use DI\ContainerBuilder;

use function Brash\Framework\functions\inTesting;

class ConnectionProvider implements AppProviderInterface
{
    public function provide(ContainerBuilder $container): void
    {
        $container->addDefinitions(
            [
                ConnectionModel::class => static function (): ConnectionModel {
                    if (inTesting()) {
                        return new ConnectionModel(
                            driver: 'pdo_sqlite',
                            memory: 'true'
                        );
                    }

                    if (isset($_ENV['DATABASE_URL'])) {
                        return new ConnectionModel(url: $_ENV['DATABASE_URL']);
                    }

                    // These are essentially mandatory, except for charset
                    $dbParams = [
                        'DRIVER',
                        'HOST',
                        'DBNAME',
                        'PORT',
                        'USER',
                        'PASSWORD',
                        'CHARSET',
                    ];

                    $connParams = array_reduce(
                        $dbParams,
                        function (array $carry, string $item) {
                            $carry[strtolower($item)] = $_ENV[$item] ?? '';

                            return $carry;
                        },
                        []
                    );

                    return new ConnectionModel(...$connParams);
                },
            ]
        );
    }
}

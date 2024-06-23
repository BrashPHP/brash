<?php

namespace Core\Providers;

use Core\Data\Domain\ConnectionModel;
use Core\Providers\AppProviderInterface;
use DI\ContainerBuilder;


class ConnectionProvider implements AppProviderInterface
{
    public function provide(ContainerBuilder $container)
    {
        $container->addDefinitions(
            [
                ConnectionModel::class => static function (): ConnectionModel {
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
                        'CHARSET'
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
                }
            ]
        );
    }
}

<?php

namespace Core\Providers;

use Core\Providers\AppProviderInterface;
use DI\ContainerBuilder;
use Exception;

use function Core\functions\mode;

class ConnectionProvider implements AppProviderInterface
{
    public function provide(ContainerBuilder $container)
    {
        $container->addDefinitions(
            [
                'connection' => static function (): array {
                    $exceptionMessage = 'An application mode should be specified at project level .env or _ENV' .
                        'variable containing one of the following values: PRODUCTION, TEST or DEV';
                    $connectionArray = [];

                    if (isset($_ENV['DATABASE_URL'])) {
                        $connectionArray['url'] = $_ENV['DATABASE_URL'];
                    } else {
                        $dbParams = ['DRIVER', 'HOST', 'DBNAME', 'PORT', 'USER', 'PASSWORD', 'CHARSET'];
                        foreach ($dbParams as $param) {
                            $connectionArray[$param] = $_ENV[$param];
                        }
                    }

                    return match (mode()) {
                        'TEST' => [
                            'driver' => 'pdo_sqlite',
                            'memory' => 'true',
                        ],
                        'PRODUCTION', 'DEV' => $connectionArray,
                        default => throw new Exception($exceptionMessage, 500)
                    };
                }
            ]
        );
    }
}

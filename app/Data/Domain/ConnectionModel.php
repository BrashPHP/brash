<?php

namespace Core\Data\Domain;

use Core\Exceptions\ConfigException;
use function Core\functions\mode;

readonly class ConnectionModel
{
    public function __construct(
        public string $url = '',
        public string $driver = '',
        public string $host = '',
        public string $dbname = '',
        public string $port = '',
        public string $user = '',
        public string $password = '',
        public string $charset = 'utf8mb4',
    ) {
        if ($url === '' && count(array_filter([], fn($el) => $el !== '')) === 0) {
            throw new ConfigException(
                "'DATABASE_URL' must be used if fields" .
                "'DRIVER', 'HOST', 'DBNAME', 'PORT', 'USER', 'PASSWORD' are not present",
                500
            );
        }
    }

    public function createConfigArray(): array
    {
        $exceptionMessage = <<<'EOF'
            An application mode should be specified at project level.
            The .env file or $_ENV global must contain one of the following values:
            PRODUCTION, TEST or DEV.
            EOF;

        return match (mode()) {
            'TEST' => [
                'driver' => 'pdo_sqlite',
                'memory' => 'true',
            ],
            'PRODUCTION', 'DEV' => $this->getAsArray(),
            default => throw new ConfigException($exceptionMessage, 500)
        };
    }

    public function getAsArray()
    {
        return [
            'URL' => $this->url,
            'DRIVER' => $this->driver,
            'HOST' => $this->host,
            'DBNAME' => $this->dbname,
            'PORT' => $this->port,
            'USER' => $this->user,
            'PASSWORD' => $this->password,
            'CHARSET' => $this->charset,
        ];
    }
}

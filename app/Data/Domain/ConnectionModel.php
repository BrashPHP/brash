<?php

namespace Core\Data\Domain;

use Core\Exceptions\ConfigException;

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
        public ?string $memory = null
    ) {
        if (
            is_null($memory) &&
            $url === '' && count(array_filter([
                $driver,
                $host,
                $dbname,
                $port,
                $user,
                $password,
                $charset
            ], fn($el) => $el === ''))
        ) {
            throw new ConfigException(
                "'DATABASE_URL' must be used if fields" .
                "'DRIVER', 'HOST', 'DBNAME', 'PORT', 'USER', 'PASSWORD' are not present",
                500
            );
        }
    }

    public function getAsArray()
    {
        return array_filter([
            "url" => $this->url,
            "driver" => $this->driver,
            "host" => $this->host,
            "dbname" => $this->dbname,
            "port" => $this->port,
            "user" => $this->user,
            "password" => $this->password,
            "charset" => $this->charset,
            "memory" => $this->memory
        ], fn($value) => !is_null($value) && $value !== '');
    }
}

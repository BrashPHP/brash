# ARTchie's Backoffice API

[//]: # [![Coverage Status](https://coveralls.io/repos/github/slimphp/Slim-Skeleton/badge.svg?branch=master)](https://coveralls.io/github/slimphp/Slim-Skeleton?branch=master)

Backoffice API made in Slim framework using PHP 8.

## Install the Application

- Install dependencies via composer (composer install).

Check out the scripts defined at the composer.json file to start the application.

You can use also use `docker-compose` to run the app with `docker`, so you can run these commands:

```bash
cd [my-app-name]
docker-compose up -d
```

After that, open `http://localhost:8080` in your browser.

Run this command in the application directory to run the test suite

```bash
composer test
```

## Database

You can choose between Doctrine and Cycle using this tool, with CycleORM being preferred for keeping PHP's connection open.
It is highly recommended that choosing Doctrine otherwise, you keep tracking of Doctrine's Entity Manager's state and always use the ManagerRegistry in your repositories instead of relying on the EntityManager class directly, due to a lack of consistency between requests when the connection is corrupted, closed or lost.

## Loggin

It is recommended to override some logging definitions, you can do so by creating a custom provider and managing the settings yourself, as so:

```php
class LoggerProvider implements AppProviderInterface
{
    public function provide(ContainerBuilder $container)
    {
        $container->addDefinitions($this->createDefinitions());
    }

    private function createDefinitions(): array{
        return [
            'logger' => [
                'name' => 'kitsune',
                'path' => (getenv('docker') || !getenv('log-file')) ? 'php://stdout' : "$currentWorkingDir/temp/logs/app.log",
                'level' => isProd() ? Logger::INFO : Logger::DEBUG,
            ]
        ];
    }
```

## TODO

### ROADMAP

[] Incluir testes para camada HTTP do CORE
[] RouteCollector
[] RouteGroup
[] Server.php
[] Criar hot reload com server.php
[] Utilizar entrypoint docker via server.php
[] Criar watchmode local via server.php

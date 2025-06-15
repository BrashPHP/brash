# ARTchie's Backoffice API


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



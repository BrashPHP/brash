<?php

namespace Core\Providers;

use DI\ContainerBuilder;
use Core\Providers\AppProviderInterface;

class SettingsProvider implements AppProviderInterface
{
    protected string $target = 'settings';

    public function provide(ContainerBuilder $container)
    {
        $container->addDefinitions($this->createSettings());
    }

    private function createSettings(): array
    {
        $root = dirname(dirname(__DIR__));

        return [
            'root' => $root,
            'temp' => "{$root}/tmp",
            'public' => "{$root}/public",
            'settings' => [
                'displayErrorDetails' => true,
            ],
        ];
    }
}

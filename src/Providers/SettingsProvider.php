<?php

namespace Brash\Framework\Providers;

use DI\ContainerBuilder;

class SettingsProvider implements AppProviderInterface
{
    protected string $target = 'settings';

    public function provide(ContainerBuilder $container): void
    {
        $container->addDefinitions($this->createSettings());
    }

    private function createSettings(): array
    {
        $root = dirname(__DIR__, 2);

        return [
            'root' => $root,
            'temp' => $root.'/tmp',
            'public' => $root.'/public',
            'settings' => [
                'displayErrorDetails' => true,
            ],
        ];
    }
}

<?php

namespace Brash\Framework\Providers;

use DI\ContainerBuilder;

interface AppProviderInterface
{
    public function provide(ContainerBuilder $container): void;
}

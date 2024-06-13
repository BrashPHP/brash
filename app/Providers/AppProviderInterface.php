<?php

namespace Core\Providers;

use DI\ContainerBuilder;

interface AppProviderInterface
{
    public function provide(ContainerBuilder $container);
}

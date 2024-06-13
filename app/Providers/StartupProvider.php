<?php

namespace Core\Providers;

use Core\Providers\AppProviderInterface;
use DI\ContainerBuilder;
use Symfony\Component\Dotenv\Dotenv;
use function Core\functions\fromRootPath;

class StartupProvider implements AppProviderInterface
{
    public function provide(ContainerBuilder $container)
    {
        // Error reporting for production
        error_reporting(0);
        ini_set('display_errors', '0');

        // Timezone
        date_default_timezone_set('America/Fortaleza');

        $envPath = fromRootPath('.env');

        if (file_exists($envPath)) {
            $dotenv = new Dotenv();
            $dotenv->load($envPath);
        }
    }
}

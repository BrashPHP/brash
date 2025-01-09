<?php

declare(strict_types=1);

use App\Application\Providers\DependenciesProvider;
use App\Application\Providers\DoctrineDefinitionsProvider;
use App\Application\Providers\RepositoriesProvider;
use App\Application\Providers\ServicesProvider;
use Core\Builder\AppBuilderManager;
use Core\Http\Factories\ContainerFactory;
use Core\Server\Server;
use React\EventLoop\Loop;
use Revolt\EventLoop\React\Internal\EventLoopAdapter;

use function Core\functions\isProd;

require_once __DIR__.'/../vendor/autoload.php';

try {
    Loop::set(EventLoopAdapter::get());

    $containerFactory = new ContainerFactory(enableCompilation: isProd());
    $containerFactory->addProviders(
        new DependenciesProvider,
        new RepositoriesProvider,
        new ServicesProvider,
        new DoctrineDefinitionsProvider,
    );

    $appBuilder = new AppBuilderManager($containerFactory->get());
    $appBuilder->useDefaultShutdownHandler(true);
    $app = $appBuilder->build();

    $server = new Server($app);

    $server->run();
} catch (\Throwable $th) {
    echo $th;
}

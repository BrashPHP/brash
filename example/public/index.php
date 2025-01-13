<?php

declare(strict_types=1);

// use App\Application\Providers\DependenciesProvider;
// use App\Application\Providers\DoctrineDefinitionsProvider;
// use App\Application\Providers\RepositoriesProvider;
// use App\Application\Providers\ServicesProvider;
use Brash\Framework\Builder\AppBuilderManager;
use Brash\Framework\Http\Factories\ContainerFactory;
use Brash\Framework\Providers\AppProviderInterface;
use Brash\Framework\Server\Server;
use React\EventLoop\Loop;
use Revolt\EventLoop\React\Internal\EventLoopAdapter;
use function Brash\Framework\functions\isProd;


require_once __DIR__ . '/../../vendor/autoload.php';


    Loop::set(EventLoopAdapter::get());

    $containerFactory = new ContainerFactory(enableCompilation: isProd());

    $appBuilder = new AppBuilderManager($containerFactory->get());
    $appBuilder->useDefaultShutdownHandler(true);
    $app = $appBuilder->build();

    $server = new Server($app);

    $server->run();
//     Loop::addPeriodicTimer(1, function () {
//         echo time() . ": added by Gabriel!";
//     });
    

//     Loop::addSignal(SIGINT, function(): never{
//         echo "OH NO I DIED";
//         sleep(2);
//         Loop::stop();

//         exit();
//     });

//     Loop::run();
// } catch (\Throwable $th) {
//     echo $th;
// }
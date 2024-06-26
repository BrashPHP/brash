<?php

use App\Domain\Models\User;
use App\Domain\Repositories\UserRepository;
use Core\Http\Adapters\SlimFramework\SlimRouteCollector;
use Core\Http\Attributes\Route;
use Core\Http\Attributes\RouteGroup;
use Core\Http\Domain\ActionPayload;
use Core\Http\Domain\RouteModel;
use Core\Http\Factories\RouteCollectorFactory;
use Core\Http\Interfaces\ActionInterface;
use Core\Http\Routing\Cache\GroupCacheResult;
use Core\Http\Routing\GroupCollector;
use Core\Http\Routing\RouteFactory;
use DI\Container;
use Doctrine\Common\Collections\ArrayCollection;
use Nyholm\Psr7\Response;
use Spatie\StructureDiscoverer\Data\DiscoveredStructure;
use Tests\Traits\App\InstanceManager;
use Tests\Traits\App\RequestManager;

#[RouteGroup("/base", middleware: 'test-middleware')]
class BasePath
{

}
#[RouteGroup("/middle", middleware: 'middle-middleware', parent: BasePath::class)]
class MiddlePathGroup
{
}

#[Route(path: "/final-path", method: "GET", group: MiddlePathGroup::class)]
class RouteTest
{

}

class ControllerStub implements ActionInterface
{
    public function action(Psr\Http\Message\ServerRequestInterface $request): Psr\Http\Message\ResponseInterface|React\Promise\Promise
    {
        return new Response();
    }
}

test('should collect RouteTest with correct order of params', function () {
    $factory = new RouteCollectorFactory(InstanceManager::requireContainer());
    $im = new InstanceManager();
    $app = $im->getAppInstance();
    $routeCollector = new SlimRouteCollector($app);

    $factory->getRouteCollector($routeCollector, [RouteTest::class])->run($routeCollector);

    $routeFactory = new RouteFactory(new GroupCollector(), new GroupCacheResult());
    $route = $routeFactory->make(new Route(path: "final", method: "GET", group: MiddlePathGroup::class), ControllerStub::class);
    $result = [];

    foreach ($route->middlewares as $middleware) {
        $result[] = $middleware;
    }

    expect($result)->toBe(['test-middleware', 'middle-middleware']);

});

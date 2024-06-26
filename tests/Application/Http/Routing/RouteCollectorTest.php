<?php


use Core\Http\Routing\Cache\GroupCacheResult;
use Core\Http\Routing\GroupCollector;
use Core\Http\Routing\RouteFactory;

use Tests\Application\Http\Routing\Fixtures\ControllerStub;
use Tests\Application\Http\Routing\Fixtures\RouteAttributeStub;



test('should collect RouteTest with correct order of params', function () {
    $routeFactory = new RouteFactory(new GroupCollector(), new GroupCacheResult());
    $route = $routeFactory->make(new RouteAttributeStub(), ControllerStub::class);
    $result = [];

    foreach ($route->middlewares as $middleware) {
        $result[] = $middleware;
    }

    expect($result)->toBe(['test-middleware', 'middle-middleware', 'Last/Testing/Middleware']);

});

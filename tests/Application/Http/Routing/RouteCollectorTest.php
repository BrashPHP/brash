<?php

use Brash\Framework\Http\Routing\Cache\GroupCacheResult;
use Brash\Framework\Http\Routing\GroupCollector;
use Brash\Framework\Http\Routing\RouteFactory;
use Tests\Application\Http\Routing\Fixtures\ControllerStub;
use Tests\Application\Http\Routing\Fixtures\RouteAttributeStub;

test('should collect RouteTest with correct order of params', function () {
    $routeFactory = new RouteFactory(new GroupCollector, new GroupCacheResult);
    $route = $routeFactory->make(new RouteAttributeStub, ControllerStub::class);
    $result = [];

    foreach ($route->middlewares as $middleware) {
        $result[] = $middleware;
    }

    expect($result)->toBe(['test-middleware', 'middle-middleware', 'Last/Testing/Middleware']);

});

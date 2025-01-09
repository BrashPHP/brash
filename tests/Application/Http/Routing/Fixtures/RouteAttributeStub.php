<?php

namespace Tests\Application\Http\Routing\Fixtures;

use Brash\Framework\Http\Attributes\Route;
use Brash\Framework\Http\Attributes\RouteGroup;

#[RouteGroup('/base', middleware: 'test-middleware')]
class BasePath {}

#[RouteGroup('/middle', middleware: 'middle-middleware', parent: BasePath::class)]
class MiddlePathGroup {}

readonly class RouteAttributeStub extends Route
{
    public function __construct()
    {
        parent::__construct(path: 'final', method: 'GET', group: MiddlePathGroup::class, middleware: 'Last/Testing/Middleware');
    }
}

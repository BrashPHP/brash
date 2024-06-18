<?php

namespace Core\Http\Interfaces;


interface RouterInterface
{
    public function run(RouteCollectorInterface $routeCollector): void;
}

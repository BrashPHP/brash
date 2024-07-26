<?php

namespace Core\Http\Interfaces;

use Core\Builder\MiddlewareCollector;

interface ComponentsFactoryInterface

{
    public function createMiddlewareCollector(): MiddlewareCollector;
    public function createRouterInterface(): RouterInterface;
    public function createConfigurableApplicationInterface(): ConfigurableApplicationInterface;
}

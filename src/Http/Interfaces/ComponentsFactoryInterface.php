<?php

namespace Brash\Framework\Http\Interfaces;

use Brash\Framework\Builder\MiddlewareCollector;

interface ComponentsFactoryInterface
{
    public function createMiddlewareCollector(): MiddlewareCollector;

    public function createRouterInterface(): RouterInterface;

    public function createConfigurableApplicationInterface(): ConfigurableApplicationInterface;
}

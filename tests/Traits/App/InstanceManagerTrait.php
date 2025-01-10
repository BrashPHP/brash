<?php

namespace Tests\Traits\App;

use Brash\Framework\Builder\AppBuilderManager;
use Brash\Framework\Http\Factories\ContainerFactory;
use Brash\Framework\Http\Interfaces\ApplicationInterface;
use Psr\Container\ContainerInterface;

trait InstanceManagerTrait
{
    protected static ?ContainerInterface $container = null;

    protected ApplicationInterface $app;

    /**
     * @throws \Exception
     */
    final protected function getAppInstance(): ApplicationInterface
    {
        $appBuilder = new AppBuilderManager($this->getContainer());

        return $appBuilder->build();
    }

    final protected function createAppInstance(): ApplicationInterface
    {
        $appBuilder = new AppBuilderManager($this->getContainer(true));

        return $appBuilder->build();
    }

    protected function getContainer(bool $forceUpdate = false): ContainerInterface
    {
        return self::requireContainer($forceUpdate);
    }

    protected function autowireContainer($key, $instance)
    {
        /**
         * @var \DI\Container
         */
        $container = $this->getContainer();
        $container->set($key, $instance);
    }

    public static function setUpContainer(): ContainerInterface
    {
        $containerFactory = new ContainerFactory;

        return $containerFactory->get();
    }

    public static function requireContainer(bool $forceUpdate = false): ContainerInterface
    {
        if (! self::$container instanceof ContainerInterface || $forceUpdate) {
            self::$container = self::setUpContainer();
        }

        return self::$container;
    }
}

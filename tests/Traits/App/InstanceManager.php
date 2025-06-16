<?php

namespace Tests\Traits\App;

use Brash\Framework\Builder\AppBuilder;
use Brash\Framework\Http\Factories\ContainerFactory;
use Brash\Framework\Http\Interfaces\ApplicationInterface;
use Psr\Container\ContainerInterface;

class InstanceManager
{
    public static ?ContainerInterface $container = null;

    protected ApplicationInterface $app;

    /**
     * @throws \Exception
     */
    final public function getAppInstance(): ApplicationInterface
    {
        $appBuilder = new AppBuilder($this->getContainer());

        return $appBuilder->build();
    }

    final public function createAppInstance(): \Brash\Framework\Http\Interfaces\ApplicationInterface
    {
        $appBuilder = new AppBuilder($this->getContainer(true));

        return $appBuilder->build();
    }

    public function getContainer(bool $forceUpdate = false): ContainerInterface
    {
        return self::requireContainer($forceUpdate);
    }

    public function autowireContainer(string $key, $instance): void
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

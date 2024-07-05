<?php

namespace Tests\Traits\App;

use Core\Builder\AppBuilderManager;
use Core\Http\Factories\ContainerFactory;
use Psr\Container\ContainerInterface;
use Slim\App;

class InstanceManager
{
    public static ?ContainerInterface $container = null;

    protected App $app;

    /**
     * @throws \Exception
     */
    final public function getAppInstance(): App
    {
        $appBuilder = new AppBuilderManager($this->getContainer());

        return $appBuilder->build();
    }

    final public function createAppInstance()
    {
        $appBuilder = new AppBuilderManager($this->getContainer(true));

        return $appBuilder->build();
    }

    public function getContainer(bool $forceUpdate = false): ContainerInterface
    {
        return self::requireContainer($forceUpdate);
    }

    public function autowireContainer($key, $instance)
    {
        $container = $this->getContainer();
        $container->set($key, $instance);
    }

    public static function setUpContainer(): ContainerInterface
    {
        $containerFactory = new ContainerFactory();

        return $containerFactory->get();
    }

    public static function requireContainer(bool $forceUpdate = false): ContainerInterface
    {
        if (!self::$container instanceof ContainerInterface || $forceUpdate) {
            self::$container = self::setUpContainer();
        }

        return self::$container;
    }
}

<?php

namespace Tests\Traits\App;

use Core\Builder\AppBuilderManager;
use Core\Http\Factories\ContainerFactory;
use Psr\Container\ContainerInterface;
use Slim\App;

trait InstanceManagerTrait
{
    protected static ?ContainerInterface $container = null;
    protected App $app;

    /**
     * @throws \Exception
     */
    final protected function getAppInstance(): App
    {
        $appBuilder = new AppBuilderManager($this->getContainer());

        return $appBuilder->build();
    }

    final protected function createAppInstance()
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
         * @var ContainerInterface
         */
        $container = $this->getContainer();
        $container->set($key, $instance);
    }

    static function setUpContainer(): ContainerInterface
    {
        $containerFactory = new ContainerFactory();

        return $containerFactory->get();
    }

    static function requireContainer(bool $forceUpdate = false): ContainerInterface
    {
        if (null === self::$container || $forceUpdate) {
            self::$container = self::setUpContainer();
        }

        return self::$container;
    }
}

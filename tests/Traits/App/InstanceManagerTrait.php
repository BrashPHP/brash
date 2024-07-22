<?php

namespace Tests\Traits\App;

use Core\Application\App;
use Core\Builder\AppBuilderManager;
use Core\Http\Factories\ContainerFactory;
use Psr\Container\ContainerInterface;
use App\Application\Providers\{
    DependenciesProvider,
    RepositoriesProvider,
    ServicesProvider,
    SettingsProvider
};

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
         * @var \DI\Container
         */
        $container = $this->getContainer();
        $container->set($key, $instance);
    }

    public static function setUpContainer(): ContainerInterface
    {
        $containerFactory = new ContainerFactory();
        $containerFactory->addProviders(
            new DependenciesProvider(),
            new RepositoriesProvider(),
            new ServicesProvider(),
            new SettingsProvider
        );

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

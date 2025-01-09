<?php

namespace Tests\Traits\App;

use App\Application\Providers\DependenciesProvider;
use App\Application\Providers\DoctrineDefinitionsProvider;
use App\Application\Providers\RepositoriesProvider;
use App\Application\Providers\ServicesProvider;
use Core\Builder\AppBuilderManager;
use Core\Http\Factories\ContainerFactory;
use Core\Http\Interfaces\ApplicationInterface;
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
        $containerFactory->addProviders(
            new DependenciesProvider,
            new RepositoriesProvider,
            new ServicesProvider,
            new DoctrineDefinitionsProvider,
        );

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

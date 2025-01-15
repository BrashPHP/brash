<?php

namespace Brash\Framework\Builder;

use Brash\Framework\Exceptions\ConfigException;
use Brash\Framework\Http\Factories\SlimAppFactory;
use Brash\Framework\Http\Interfaces\ApplicationInterface;
use Brash\Framework\Http\Interfaces\ComponentsFactoryInterface;
use Brash\Framework\Http\Middlewares\BodyParsing\BodyParsingMiddleware;
use Brash\Framework\Http\Middlewares\ResponseAdapterMiddleware;
use Brash\Framework\Http\Middlewares\TrailingSlashMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

class AppBuilderManager
{
    private bool $displayErrors;

    private ComponentsFactoryInterface $componentsFactory;

    /**
     * Application Global Middlewares
     *
     * @var MiddlewareInterface|class-string<MiddlewareInterface>
     */
    private array $middlewares = [
        BodyParsingMiddleware::class,
        ResponseAdapterMiddleware::class,
        TrailingSlashMiddleware::class,
    ];

    public function __construct(
        private ContainerInterface $container,
        private bool $enableErrorHandler = true,
        private bool $enableShutdownHandler = true,
    ) {
        $this->displayErrors = $this->container->get('settings')['displayErrorDetails'];
        $this->componentsFactory = new SlimAppFactory($container);
    }

    public function appendMiddleware(MiddlewareInterface $middlewareInterface): void
    {
        $this->middlewares[] = $middlewareInterface;
    }

    public function build(): ApplicationInterface
    {
        $middlewareCollector = $this->componentsFactory->createMiddlewareCollector();

        foreach ($this->middlewares as $preMiddleware) {
            $middlewareCollector->add($preMiddleware);
        }

        $middlewareCollector->collect();

        $router = $this->componentsFactory->createRouterInterface();

        $router->run();

        $configurableApplicationInterface = $this->componentsFactory->createConfigurableApplicationInterface();

        return $configurableApplicationInterface->createByConfig(
            $this->enableErrorHandler,
            $this->enableShutdownHandler,
            $this->displayErrors
        );
    }

    public function useDefaultErrorHandler(bool $enable): void
    {
        $this->enableErrorHandler = $enable;
    }

    public function useDefaultShutdownHandler(bool $enable): void
    {
        if (! $this->enableErrorHandler) {
            throw new ConfigException('Unable to use default shutdown handler when error handler is not enabled');
        }

        $this->enableShutdownHandler = $enable;
    }
}

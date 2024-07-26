<?php

namespace Core\Builder;

use Core\Exceptions\ConfigException;
use Core\Http\Factories\SlimAppFactory;
use Core\Http\Interfaces\ApplicationInterface;
use Core\Http\Interfaces\ComponentsFactoryInterface;
use Core\Http\Middlewares\TrailingSlashMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use App\Presentation\Middleware\JWTAuthMiddleware;
use App\Presentation\Middleware\SessionMiddleware;
use App\Presentation\Middleware\ResponseAdapterMiddleware;
use Core\Http\Middlewares\BodyParsing\BodyParsingMiddleware;


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
        SessionMiddleware::class,
        JWTAuthMiddleware::class,
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

    public function appendMiddleware(MiddlewareInterface $middlewareInterface)
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

    public function useDefaultErrorHandler(bool $enable)
    {
        $this->enableErrorHandler = $enable;
    }

    public function useDefaultShutdownHandler(bool $enable)
    {
        if (!$this->enableErrorHandler) {
            throw new ConfigException('Unable to use default shutdown handler when error handler is not enabled');
        }
        $this->enableShutdownHandler = $enable;
    }
}

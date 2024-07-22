<?php

namespace Core\Builder;

use Core\Builder\MiddlewareCollector;
use Core\Exceptions\ConfigException;
use Core\Http\Adapters\SlimFramework\SlimMiddlewareIncluder;
use Core\Http\Adapters\SlimFramework\SlimRouteCollector;
use Core\Http\ErrorHandlers\HttpErrorHandler;
use Core\Http\Factories\RouteCollectorFactory;
use Core\Http\Interfaces\ApplicationInterface;
use Core\Http\Middlewares\ShutdownMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Factory\AppFactory;


class AppBuilderManager
{
    private bool $displayErrors;
    public function __construct(
        private ContainerInterface $container,
        private bool $enableErrorHandler = true,
        private bool $enableShutdownHandler = true,
        private array $preMiddlewares = []
    ) {
        $this->displayErrors = $this->container->get('settings')['displayErrorDetails'];
    }

    public function appendMiddlewares(MiddlewareInterface $middlewareInterface)
    {
        $this->preMiddlewares[] = $middlewareInterface;
    }

    public function build(): ApplicationInterface
    {
        $app = $this->createApp();

        foreach ($this->preMiddlewares as $preMiddleware) {
            $app->addMiddleware($preMiddleware);
        }

        $middlewareCollector = new MiddlewareCollector();
        $middlewareCollector->collect(new SlimMiddlewareIncluder($app));

        $app->addRoutingMiddleware(); // Add the Slim built-in routing middleware

        $routerFactory = new RouteCollectorFactory($this->container);
        $routeCollector = new SlimRouteCollector($app);


        $routerFactory->getRouteCollector($routeCollector)->run($routeCollector);

        if ($this->enableErrorHandler) {
            $this->setErrorHandler($app);
        }

        return new \Core\Application\App($app);
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

    private function setErrorHandler(App $app)
    {
        $callableResolver = $app->getCallableResolver();
        $responseFactory = $app->getResponseFactory();

        $logger = $this->container->get(LoggerInterface::class);

        $errorHandler = new HttpErrorHandler($callableResolver, $responseFactory, $logger);

        if ($this->enableShutdownHandler) {
            $app->add(new ShutdownMiddleware($errorHandler, $this->displayErrors));
        }

        $errorMiddleware = $app->addErrorMiddleware($this->displayErrors, false, false);
        $errorMiddleware->setDefaultErrorHandler($errorHandler);
    }

    private function createApp(): App
    {
        AppFactory::setContainer($this->container);

        return AppFactory::create();
    }
}

<?php

namespace Core\Http\Adapters\SlimFramework;

use Core\Http\ErrorHandlers\HttpErrorHandler;
use Core\Http\Interfaces\ApplicationInterface;
use Core\Http\Interfaces\ConfigurableApplicationInterface;
use Core\Http\Middlewares\ShutdownMiddleware;
use Psr\Log\LoggerInterface;
use Slim\App;
use function Core\functions\isProd;

final class SlimAppInterfaceBuilder implements ConfigurableApplicationInterface
{
    public function __construct(private App $app, private LoggerInterface $logger)
    {
    }
    /**
     * Configures error handling used in the application instance
     */
    public function createByConfig(
        bool $useDefaultErrorHandler,
        bool $useDefaultShutdownHandler,
        bool $displayErrors
    ): ApplicationInterface {
        if ($useDefaultErrorHandler) {
            $app = $this->app;
            $callableResolver = $app->getCallableResolver();
            $responseFactory = $app->getResponseFactory();

            $errorHandler = new HttpErrorHandler($callableResolver, $responseFactory, $this->logger);

            if ($useDefaultShutdownHandler) {
                $app->add(new ShutdownMiddleware($errorHandler, $displayErrors));
            }

            $errorMiddleware = $app->addErrorMiddleware($displayErrors, true, isProd());
            $errorMiddleware->setDefaultErrorHandler($errorHandler);
        }


        return new SlimAppAdapter($app);
    }
}

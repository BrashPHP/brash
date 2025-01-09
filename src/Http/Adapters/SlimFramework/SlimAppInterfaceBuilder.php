<?php

namespace Brash\Framework\Http\Adapters\SlimFramework;

use Brash\Framework\Http\ErrorHandlers\HttpErrorHandler;
use Brash\Framework\Http\Interfaces\ApplicationInterface;
use Brash\Framework\Http\Interfaces\ConfigurableApplicationInterface;
use Brash\Framework\Http\Middlewares\ShutdownMiddleware;
use Psr\Log\LoggerInterface;
use Slim\App;

use function Brash\Framework\functions\isProd;

final class SlimAppInterfaceBuilder implements ConfigurableApplicationInterface
{
    public function __construct(private App $app, private LoggerInterface $logger) {}

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

<?php

namespace Core\Http\Interfaces;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ConfigurableApplicationInterface
{
    /**
     * Configures error handling used in the application instance
     *
     */
    public function createByConfig(
        bool $useDefaultErrorHandler,
        bool $useDefaultShutdownHandler,
        bool $displayErrors
    ): ApplicationInterface;
}

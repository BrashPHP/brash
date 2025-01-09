<?php

namespace Core\Http\Interfaces;

interface ConfigurableApplicationInterface
{
    /**
     * Configures error handling used in the application instance
     */
    public function createByConfig(
        bool $useDefaultErrorHandler,
        bool $useDefaultShutdownHandler,
        bool $displayErrors
    ): ApplicationInterface;
}

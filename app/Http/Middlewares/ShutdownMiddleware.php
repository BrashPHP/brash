<?php

declare(strict_types=1);

namespace Core\Http\Middlewares;


use Core\Http\ErrorHandlers\HttpErrorHandler;
use Core\Http\ErrorHandlers\ShutdownHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ShutdownMiddleware  implements MiddlewareInterface
{
    public function __construct(private HttpErrorHandler $httpErrorHandler, private bool $displayErrors) {
    }
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface{
        $shutdownHandler = new ShutdownHandler($request, $this->httpErrorHandler, $this->displayErrors);

        register_shutdown_function($shutdownHandler);

        return $handler->handle($request);
    }
}

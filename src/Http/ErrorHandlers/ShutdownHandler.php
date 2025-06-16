<?php

declare(strict_types=1);

namespace Brash\Framework\Http\ErrorHandlers;

use Brash\Framework\Http\Exceptions\HttpInternalServerErrorException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\ErrorHandlerInterface;
use Slim\ResponseEmitter;

class ShutdownHandler
{
    public function __construct(
        private readonly Request $request,
        private readonly ErrorHandlerInterface $errorHandler,
        private readonly bool $displayErrorDetails
    ) {}

    public function __invoke(): void
    {
        $error = error_get_last();
        if ($error !== null && $error !== []) {
            $errorFile = $error['file'];
            $errorLine = $error['line'];
            $errorMessage = $error['message'];
            $errorType = $error['type'];
            $message = 'An error while processing your request. Please try again later.';

            if ($this->displayErrorDetails) {
                switch ($errorType) {
                    case E_USER_ERROR:
                        $message = sprintf('FATAL ERROR: %s. ', $errorMessage);
                        $message .= sprintf(' on line %d in file %s.', $errorLine, $errorFile);

                        break;

                    case E_USER_WARNING:
                        $message = sprintf('WARNING: %s', $errorMessage);

                        break;

                    case E_USER_NOTICE:
                        $message = sprintf('NOTICE: %s', $errorMessage);

                        break;

                    default:
                        $message = sprintf('ERROR: %s', $errorMessage);
                        $message .= sprintf(' on line %d in file %s.', $errorLine, $errorFile);

                        break;
                }
            }

            $exception = new HttpInternalServerErrorException($this->request, $message);

            $response = $this->errorHandler->__invoke($this->request, $exception, $this->displayErrorDetails, false, false);

            $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';

            $response = $response
                ->withHeader('Access-Control-Allow-Credentials', 'true')
                ->withHeader('Access-Control-Allow-Origin', $origin)
                ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization, X-Renew-Token, Set-Cookie')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                ->withHeader('Access-Control-Expose-Headers', 'X-Renew-Token')
                ->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->withAddedHeader('Cache-Control', 'post-check=0, pre-check=0')
                ->withHeader('Pragma', 'no-cache');

            if (ob_get_contents()) {
                ob_clean();
            }

            $responseEmitter = new ResponseEmitter;
            $responseEmitter->emit($response);
        }
    }
}

<?php

declare(strict_types=1);

namespace Core\Http\ErrorHandlers;

use Core\Http\Domain\ActionPayload;
use Core\Http\Errors\{ErrorsEnum, ActionError};
use Core\Http\Exceptions\{
    HttpNotFoundException,
    HttpMethodNotAllowedException,
    HttpUnauthorizedException,
    UnprocessableEntityException,
    HttpForbiddenException,
    HttpBadRequestException,
    HttpNotImplementedException,
};
use Core\Http\Exceptions\BaseHttpException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpException as SlimHttpException;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;


use Throwable;

use function Core\functions\mode;

class HttpErrorHandler extends SlimErrorHandler
{
    /**
     * {@inheritdoc}
     */
    protected function respond(): Response
    {
        $exception = $this->exception;
        $statusCode = 500;
        $message = "";

        if ($exception instanceof SlimHttpException || $exception instanceof BaseHttpException) {
            $statusCode = $exception->getCode();

            $message = $exception->getMessage();
        } elseif ($exception instanceof Exception || $exception instanceof Throwable && $this->displayErrorDetails) {
            $message = $exception->getMessage();
        }

        $errorType = ErrorsEnum::tryFrom($statusCode) ?? ErrorsEnum::SERVER_ERROR;

        $this->logErrorMessage($exception, $statusCode, $errorType);

        $error = new ActionError($errorType, $message);

        $payload = new ActionPayload($statusCode, null, $error);
        $encodedPayload = json_encode($payload, JSON_PRETTY_PRINT);

        $response = $this->responseFactory->createResponse($statusCode);
        $response->getBody()->write($encodedPayload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    private function logErrorMessage(Throwable $error, int $statusCode, ErrorsEnum $errorType)
    {
        $isServerError = $statusCode >= 500 && $statusCode < 600;
        $template = [
            'error_type' => $errorType->value,
            'status_code' => $statusCode,
            'error' => $error->getMessage(),
            'stack_trace' => $isServerError ? $error->getTrace() : [],
        ];

        $this->logError(json_encode($template));
    }

    protected function logError(string $error): void
    {
        if (mode() === "TEST") {
            return;
        }

        $this->logger->error($error);
    }
}

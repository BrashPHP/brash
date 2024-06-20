<?php

declare(strict_types=1);

namespace Core\Http\ErrorHandlers;

use App\Presentation\Actions\Protocols\ActionPayload;
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
        $errorType = ErrorsEnum::SERVER_ERROR;

        if ($exception instanceof BaseHttpException) {
            $statusCode = $exception->getCode();

            $message = $exception->getMessage();

            $errorType = match (true) {
                $exception instanceof HttpNotFoundException => ErrorsEnum::RESOURCE_NOT_FOUND,
                $exception instanceof HttpMethodNotAllowedException => ErrorsEnum::NOT_ALLOWED,
                $exception instanceof HttpUnauthorizedException => ErrorsEnum::UNAUTHENTICATED,
                $exception instanceof UnprocessableEntityException => ErrorsEnum::UNPROCESSABLE_ENTITY,
                $exception instanceof HttpForbiddenException => ErrorsEnum::INSUFFICIENT_PRIVILEGES,
                $exception instanceof HttpBadRequestException => ErrorsEnum::BAD_REQUEST,
                $exception instanceof HttpNotImplementedException => ErrorsEnum::NOT_IMPLEMENTED,
                default => ErrorsEnum::SERVER_ERROR,
            };
        } elseif (
            !($exception instanceof BaseHttpException)
            && ($exception instanceof Exception || $exception instanceof Throwable)
            && $this->displayErrorDetails
        ) {
            $message = $exception->getMessage();
        }

        $this->logErrorMessage($exception, $statusCode, $errorType);
        
        $error = new ActionError($errorType->value, $message);
        
        $payload = new ActionPayload($statusCode, null, $error);
        $encodedPayload = json_encode($payload, JSON_PRETTY_PRINT);

        $response = $this->responseFactory->createResponse($statusCode);
        $response->getBody()->write($encodedPayload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    private function logErrorMessage(Throwable $error, int $statusCode, ErrorsEnum $errorType)
    {
        $template = [
            'error_type' => $errorType->value,
            'status_code' => $statusCode,
            'error' => $error->getMessage(),
            'stack_trace' => $statusCode === 500 ? $error->getTrace() : [],
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

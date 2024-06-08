<?php

declare(strict_types=1);

namespace App\Presentation\Handlers;

use App\Presentation\Actions\Protocols\ActionError;
use App\Presentation\Actions\Protocols\ActionPayload;
use App\Presentation\Actions\Protocols\ErrorsEnum;
use App\Presentation\Actions\Protocols\HttpErrors\UnprocessableEntityException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpUnauthorizedException;
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

        if ($exception instanceof HttpException) {
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
        } elseif (!($exception instanceof HttpException)
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

        $this->logError(json_encode($template, JSON_PRETTY_PRINT));
    }

    protected function logError(string $error): void
    {
        if (mode() === "TEST") {
            return;
        }

        $this->logger->error($error);
    }
}

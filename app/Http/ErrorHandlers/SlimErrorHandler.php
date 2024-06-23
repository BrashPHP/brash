<?php

declare(strict_types=1);

namespace Core\Http\ErrorHandlers;

use Core\Http\Domain\ActionPayload;
use Core\Http\Errors\{ErrorsEnum, ActionError};
use Core\Http\Exceptions\UnprocessableEntityException;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Exception;
use Slim\Exception\HttpException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpUnauthorizedException;


class SlimHttpErrorHandler
{
    public function respond(HttpException $exception): ActionPayload
    {
        $statusCode = 500;
        $message = "";

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
        ) {
            $message = $exception->getMessage();
        }

        $error = new ActionError($errorType->value, $message);

        return new ActionPayload($statusCode, null, $error);
    }
}

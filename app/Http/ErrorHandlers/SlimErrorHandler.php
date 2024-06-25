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

            $errorType = ErrorsEnum::tryFrom($statusCode) ?? ErrorsEnum::SERVER_ERROR;
        } elseif ($exception instanceof Exception || $exception instanceof Throwable) {
            $message = $exception->getMessage();
        }

        $error = new ActionError($errorType, $message);

        return new ActionPayload($statusCode, null, $error);
    }
}

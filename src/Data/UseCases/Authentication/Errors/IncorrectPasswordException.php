<?php

declare(strict_types=1);

namespace App\Data\UseCases\Authentication\Errors;

use Core\Http\Errors\HttpExceptionAdapter;
use Core\Http\Exceptions\BaseHttpException;
use Core\Http\Exceptions\HttpBadRequestException;
use Psr\Http\Message\ServerRequestInterface;

class IncorrectPasswordException extends HttpExceptionAdapter
{
    public function wire(ServerRequestInterface $request): BaseHttpException
    {
        return new HttpBadRequestException($request, "The passwords don't match");
    }
}

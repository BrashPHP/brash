<?php

namespace App\Data\UseCases\SocialLogin\Errors;

use Core\Http\Errors\HttpExceptionAdapter;
use Core\Http\Exceptions\BaseHttpException;
use Core\Http\Exceptions\HttpInternalServerErrorException;
use Psr\Http\Message\ServerRequestInterface;

class CantGetUserInformationException extends HttpExceptionAdapter
{
    public function wire(ServerRequestInterface $request): BaseHttpException
    {
        return new HttpInternalServerErrorException($request, "Could not request user's information");
    }
}

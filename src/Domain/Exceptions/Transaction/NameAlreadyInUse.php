<?php

namespace App\Domain\Exceptions\Transaction;

use Core\Http\Errors\HttpExceptionAdapter;
use Core\Http\Exceptions\BaseHttpException;
use Core\Http\Exceptions\HttpBadRequestException;
use Psr\Http\Message\ServerRequestInterface;


class NameAlreadyInUse extends HttpExceptionAdapter
{
    public function wire(ServerRequestInterface $request): BaseHttpException
    {
        $message = 'An error occured while inserting values in transaction. Unique constraint has been violated. Cause: ';
        $message .= $this->message;

        return new HttpBadRequestException($request, $message);
    }
}

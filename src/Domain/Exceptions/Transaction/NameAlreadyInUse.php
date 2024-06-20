<?php

namespace App\Domain\Exceptions\Transaction;

use App\Domain\Exceptions\Protocols\HttpSpecializedAdapterCustom;
use Core\Http\Exceptions\BaseHttpException;
use Core\Http\Exceptions\HttpBadRequestException;
use Psr\Http\Message\ServerRequestInterface;


class NameAlreadyInUse extends HttpSpecializedAdapterCustom
{
    public function wire(ServerRequestInterface $request): BaseHttpException
    {
        $message = 'An error occured while inserting values in transaction. Unique constraint has been violated. Cause: ';
        $message .= $this->message;

        return new HttpBadRequestException($request, $message);
    }
}

<?php

namespace App\Domain\Exceptions\Protocols\UniqueConstraintViolation;

use App\Domain\Exceptions\Protocols\HttpSpecializedAdapter;
use Core\Http\Errors\HttpExceptionAdapter;
use Core\Http\Exceptions\BaseHttpException;
use Core\Http\Exceptions\HttpBadRequestException;
use Psr\Http\Message\ServerRequestInterface;


abstract class AbstractUniqueException extends HttpExceptionAdapter
{
    protected string $responsaMessage;

    public function wire(ServerRequestInterface $request): BaseHttpException
    {
        return new HttpBadRequestException($request, $this->responsaMessage);
    }

    public function getResponseMessage()
    {
        return $this->responsaMessage;
    }
}

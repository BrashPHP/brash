<?php

namespace App\Domain\Exceptions\Protocols\UniqueConstraintViolation;

use App\Domain\Exceptions\Protocols\HttpSpecializedAdapter;
use App\Domain\Exceptions\Protocols\HttpSpecializedAdapterCustom;
use Core\Http\Exceptions\BaseHttpException;
use Core\Http\Exceptions\HttpBadRequestException;
use Psr\Http\Message\ServerRequestInterface;


abstract class AbstractUniqueException extends HttpSpecializedAdapterCustom
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

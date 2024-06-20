<?php

namespace App\Domain\Exceptions\Transaction;

use App\Domain\Exceptions\Protocols\HttpSpecializedAdapter;
use Core\Http\Errors\HttpExceptionAdapter;
use Core\Http\Exceptions\BaseHttpException;
use Core\Http\Exceptions\HttpBadRequestException;
use Psr\Http\Message\ServerRequestInterface;

class InstanceNotFoundException extends HttpExceptionAdapter
{
    public function __construct(private string $object)
    {
    }

    public function wire(ServerRequestInterface $request): BaseHttpException
    {
        $message = sprintf('The requested %s does not exist', $this->object);
        $message .= $this->message;

        return new HttpBadRequestException($request, $message);
    }
}

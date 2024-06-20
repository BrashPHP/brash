<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

use App\Domain\Exceptions\Protocols\DomainRecordNotFoundException;
use Core\Http\Exceptions\BaseHttpException;
use Core\Http\Exceptions\HttpNotFoundException;
use Psr\Http\Message\ServerRequestInterface;

class NoAccountFoundException extends DomainRecordNotFoundException
{
    public function wire(ServerRequestInterface $request): BaseHttpException
    {
        $message = 'The account you requested does not exist.';

        return new HttpNotFoundException($request, $message);
    }
}

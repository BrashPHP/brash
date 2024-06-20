<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

use App\Domain\Exceptions\Protocols\DomainRecordNotFoundException;
use Core\Http\Exceptions\BaseHttpException;
use Core\Http\Exceptions\HttpNotFoundException;
use Psr\Http\Message\ServerRequestInterface;

class UserNotFoundException extends DomainRecordNotFoundException
{
    public $message = 'The user you requested does not exist.';

    public function wire(ServerRequestInterface $request): BaseHttpException
    {
        return new HttpNotFoundException($request, $this->message);
    }
}

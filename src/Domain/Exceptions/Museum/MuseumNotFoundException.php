<?php

declare(strict_types=1);

namespace App\Domain\Exceptions\Museum;

use App\Domain\Exceptions\Protocols\DomainRecordNotFoundException;
use Core\Http\Exceptions\BaseHttpException;
use Core\Http\Exceptions\HttpNotFoundException;
use Psr\Http\Message\ServerRequestInterface;

class MuseumNotFoundException extends DomainRecordNotFoundException
{
    public function wire(ServerRequestInterface $request): BaseHttpException
    {
        $message = 'The museum you requested does not exist.';

        return new HttpNotFoundException($request, $message);
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Exceptions\Protocols;

use Core\Http\Exceptions\BaseHttpException;
use Psr\Http\Message\ServerRequestInterface;


abstract class HttpSpecializedAdapterCustom extends DomainException
{
    abstract public function wire(ServerRequestInterface $request): BaseHttpException;
}

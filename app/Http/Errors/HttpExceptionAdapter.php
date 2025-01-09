<?php

declare(strict_types=1);

namespace Core\Http\Errors;

use Core\Http\Domain\DomainException;
use Core\Http\Exceptions\BaseHttpException;
use Psr\Http\Message\ServerRequestInterface;

abstract class HttpExceptionAdapter extends DomainException
{
    abstract public function wire(ServerRequestInterface $request): BaseHttpException;
}

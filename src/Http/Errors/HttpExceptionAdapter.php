<?php

declare(strict_types=1);

namespace Brash\Framework\Http\Errors;

use Brash\Framework\Http\Domain\DomainException;
use Brash\Framework\Http\Exceptions\BaseHttpException;
use Psr\Http\Message\ServerRequestInterface;

abstract class HttpExceptionAdapter extends DomainException
{
    abstract public function wire(ServerRequestInterface $request): BaseHttpException;
}

<?php

namespace Brash\Framework\Http\Exceptions;

use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class BadRequestException extends HttpBadRequestException
{
    public function __construct(
        ServerRequestInterface $request,
        string $message = '',
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            $request,
            message: $message,
            description: 'The request was well-formed but unable to be followed due to semantic errors.',
            previous: $previous
        );
    }
}

<?php

namespace Brash\Framework\Http\Exceptions;

use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class UnprocessableEntityException extends BaseHttpException
{
    public function __construct(
        ServerRequestInterface $request,
        string $message = '',
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            $request,
            message: $message,
            code: 422,
            title: '422 Unprocessable Entity',
            description: 'The request was well-formed but unable to be followed due to semantic errors.',
            previous: $previous
        );
    }
}

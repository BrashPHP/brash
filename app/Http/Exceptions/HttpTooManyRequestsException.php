<?php

declare(strict_types=1);

namespace Core\Http\Exceptions;

use Psr\Http\Message\ServerRequestInterface;

/** @api */
class HttpTooManyRequestsException extends BaseHttpException
{

    public function __construct(
        ServerRequestInterface $request,
        string $message = 'Too many requests.',
        \Throwable|null $previous = null,
    ) {
        parent::__construct(
            $request,
            message: $message,
            code: 429,
            title: '429 Too Many Requests',
            description: 'The client application has surpassed its rate limit, ' .
            'or number of requests they can send in a given period of time.',
            previous: $previous
        );
    }
}

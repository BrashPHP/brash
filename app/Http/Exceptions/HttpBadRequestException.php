<?php

declare(strict_types=1);

namespace Core\Http\Exceptions;

use Psr\Http\Message\ServerRequestInterface;

/** @api */
class HttpBadRequestException extends BaseHttpException
{
    public function __construct(
        ServerRequestInterface $request,
        string $message = 'Bad request.',
        string $description = 'The server cannot or will not process ' .
            'the request due to an apparent client error.',
        \Throwable|null $previous = null,
    ) {
        parent::__construct(
            $request,
            message: $message,
            code: 400,
            title: '400 Bad request',
            description: $description,
            previous: $previous
        );
    }
}

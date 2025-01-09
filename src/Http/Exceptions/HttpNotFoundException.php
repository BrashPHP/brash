<?php

declare(strict_types=1);

namespace Brash\Framework\Http\Exceptions;

use Psr\Http\Message\ServerRequestInterface;

final class HttpNotFoundException extends BaseHttpException
{
    public function __construct(
        ServerRequestInterface $request,
        string $message = 'Not found.',
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            $request,
            message: $message,
            code: 404,
            title: '404 Not Found',
            description: 'The requested resource could not be found. Please verify the URI and try again.',
            previous: $previous
        );
    }
}

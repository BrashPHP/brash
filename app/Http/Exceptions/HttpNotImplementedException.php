<?php

declare(strict_types=1);

namespace Core\Http\Exceptions;

use Psr\Http\Message\ServerRequestInterface;

/**
 * @api
 */
final class HttpNotImplementedException extends BaseHttpException
{
    public function __construct(
        ServerRequestInterface $request,
        string $message = 'Not implemented.',
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            $request,
            message: $message,
            code: 501,
            title: '501 Not Implemented',
            description: 'The server does not support the functionality required to fulfill the request.',
            previous: $previous
        );
    }
}

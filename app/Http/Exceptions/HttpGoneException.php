<?php

declare(strict_types=1);

namespace Core\Http\Exceptions;

use Psr\Http\Message\ServerRequestInterface;

/** @api */
final class HttpGoneException extends BaseHttpException
{
    public function __construct(
        ServerRequestInterface $request,
        string $message = 'Gone',
        \Throwable|null $previous = null,
    ) {
        parent::__construct(
            $request,
            message: $message,
            code: 410,
            title: '410 Gone',
            description: 'The target resource is no longer available at the origin server.',
            previous: $previous
        );
    }
}

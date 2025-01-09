<?php

declare(strict_types=1);

namespace Core\Http\Exceptions;

use Psr\Http\Message\ServerRequestInterface;

/**
 * @api
 */
class HttpUnauthorizedException extends BaseHttpException
{
    public function __construct(
        ServerRequestInterface $request,
        string $message = 'Unauthorized',
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            $request,
            message: $message,
            code: 401,
            title: '401 Unauthorized',
            description: 'The request requires valid user authentication.',
            previous: $previous
        );
    }
}

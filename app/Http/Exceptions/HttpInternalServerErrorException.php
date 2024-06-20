<?php


declare(strict_types=1);

namespace Core\Http\Exceptions;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @api 
 */
class HttpInternalServerErrorException extends BaseHttpException
{
    public function __construct(
        ServerRequestInterface $request,
        string $message = 'Internal server error.',
        \Throwable|null $previous = null,
    ) {
        parent::__construct(
            $request,
            message: $message,
            code: 500,
            title: '500 Internal Server Error',
            description: 'Unexpected condition encountered preventing server from fulfilling request.',
            previous: $previous
        );
    }
}

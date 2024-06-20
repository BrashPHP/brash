<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim/blob/4.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Core\Http\Exceptions;

use Psr\Http\Message\ServerRequestInterface;

/**
 * @api 
*/
final class HttpForbiddenException extends BaseHttpException
{

    public function __construct(
        ServerRequestInterface $request,
        string $message = 'Forbidden.',
        \Throwable|null $previous = null,
    ) {
        parent::__construct(
            $request,
            message: $message,
            code: 403,
            title:'403 Forbidden',
            description: 'You are not permitted to perform the requested operation.',
            previous: $previous
        );
    }
}

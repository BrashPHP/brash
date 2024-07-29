<?php

namespace Core\Http\Middlewares\Jwt\Exceptions;

final class InsecureUseOfMiddlewareException extends \RuntimeException
{
    public function __construct(private string $scheme){
        $message = sprintf(
            "Insecure use of middleware over %s denied by configuration.",
            strtoupper($scheme)
        );

        parent::__construct($message);
    }
}


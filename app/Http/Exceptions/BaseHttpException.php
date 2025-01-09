<?php

namespace Core\Http\Exceptions;

use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Throwable;

/**
 * @api
 *
 * @method int getCode()
 */
class BaseHttpException extends RuntimeException
{
    public function __construct(
        public readonly ServerRequestInterface $request,
        public $message = '',
        public $code = 0,
        public ?Throwable $previous = null,
        protected string $title = '',
        protected string $description = ''
    ) {
        parent::__construct($message, $code, $previous);
    }
}

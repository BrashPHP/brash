<?php

declare(strict_types=1);

namespace Brash\Framework\Http\Exceptions;

use Psr\Http\Message\ServerRequestInterface;

use function implode;

final class HttpMethodNotAllowedException extends BaseHttpException
{
    public function __construct(
        ServerRequestInterface $request,
        ?\Throwable $previous = null,
        public array $allowedMethods = []
    ) {
        parent::__construct(
            $request,
            message: 'Method not allowed.',
            code: 405,
            title: '405 Method Not Allowed',
            description: 'The request method is not supported for the requested resource.',
            previous: $previous
        );
    }

    /**
     * @return string[]
     */
    public function getAllowedMethods(): array
    {
        return $this->allowedMethods;
    }

    /**
     * @param  string[]  $methods
     */
    public function setAllowedMethods(array $methods): self
    {
        $this->allowedMethods = array_merge($this->allowedMethods, $methods);
        $this->message = 'Method not allowed. Must be one of: '.implode(', ', $methods);

        return $this;
    }
}

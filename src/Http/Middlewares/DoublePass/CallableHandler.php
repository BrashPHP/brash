<?php

namespace Brash\Framework\Http\Middlewares\DoublePass;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class CallableHandler implements RequestHandlerInterface
{
    private $callable;

    public function __construct(callable $callable, private readonly \Psr\Http\Message\ResponseInterface $response)
    {
        $this->callable = $callable;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return ($this->callable)($request, $this->response);
    }
}

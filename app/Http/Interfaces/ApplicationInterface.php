<?php

namespace Core\Http\Interfaces;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ApplicationInterface
{
    /**
     * Handle a request
     *
     * This method traverses the application middleware stack and then returns the
     * resultant Response object.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface;

    /**
     * Add route with multiple methods
     *
     * @param  string[]  $methods  Numeric array of HTTP method names
     * @param  string  $pattern  The route URI pattern
     * @param  callable|string  $callable  The route callback routine
     */
    public function map(array $methods, string $pattern, $callable): MiddlewareAttachableInterface;
}

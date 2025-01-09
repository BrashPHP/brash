<?php

namespace Core\Http\Interfaces;

use Psr\Http\Message\UriInterface;

interface RouteCollectorInterface
{
    /**
     * Add route with multiple methods
     *
     * @param  string[]  $methods  Numeric array of HTTP method names
     * @param  string  $pattern  The route URI pattern
     * @param  callable|string  $callable  The route callback routine
     */
    public function map(array $methods, string $pattern, $callable): MiddlewareAttachableInterface;

    public function redirect(string|UriInterface $from, $to, int $status = 302): void;
}

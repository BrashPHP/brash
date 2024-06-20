<?php

namespace Core\Http\Adapters\FrameworkX;

use Core\Http\Interfaces\RouteCollectorInterface;
use Core\Http\Interfaces\MiddlewareAttachableInterface;
use FrameworkX\App;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\UriInterface;

class XRouteCollector implements RouteCollectorInterface
{

    public function __construct(private readonly App $app, private ContainerInterface $container)
    {

    }

    public function getContainer(): ?ContainerInterface
    {
        return $this->container;
    }

    /**
     * Add GET route
     *
     * @param string          $pattern  The route URI pattern
     * @param callable|string $callable The route callback routine
     */
    public function get(string $pattern, $callable): MiddlewareAttachableInterface
    {
        return $this->map(['GET'], $pattern, $callable);
    }

    /**
     * Add POST route
     *
     * @param string          $pattern  The route URI pattern
     * @param callable|string $callable The route callback routine
     */
    public function post(string $pattern, $callable): MiddlewareAttachableInterface
    {
        return $this->map(['POST'], $pattern, $callable);

    }

    /**
     * Add PUT route
     *
     * @param string          $pattern  The route URI pattern
     * @param callable|string $callable The route callback routine
     */
    public function put(string $pattern, $callable): MiddlewareAttachableInterface
    {
        return $this->map(['PUT'], $pattern, $callable);

    }

    /**
     * Add PATCH route
     *
     * @param string          $pattern  The route URI pattern
     * @param callable|string $callable The route callback routine
     */
    public function patch(string $pattern, $callable): MiddlewareAttachableInterface
    {
        return $this->map(['PATCH'], $pattern, $callable);
    }

    /**
     * Add DELETE route
     *
     * @param string          $pattern  The route URI pattern
     * @param callable|string $callable The route callback routine
     */
    public function delete(string $pattern, $callable): MiddlewareAttachableInterface
    {
        return $this->map(['DELETE'], $pattern, $callable);

    }

    /**
     * Add OPTIONS route
     *
     * @param string          $pattern  The route URI pattern
     * @param callable|string $callable The route callback routine
     */
    public function options(string $pattern, $callable): MiddlewareAttachableInterface
    {
        return $this->map(['OPTIONS'], $pattern, $callable);
    }

    /**
     * Add route for any HTTP method
     *
     * @param string          $pattern  The route URI pattern
     * @param callable|string $callable The route callback routine
     */
    public function any(string $pattern, $callable): MiddlewareAttachableInterface
    {
        return $this->map(['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], $pattern, $callable);
    }

    /**
     * Add route with multiple methods
     *
     * @param string[]        $methods  Numeric array of HTTP method names
     * @param string          $pattern  The route URI pattern
     * @param callable|string $callable The route callback routine
     */
    public function map(array $methods, string $pattern, $callable): MiddlewareAttachableInterface
    {
        return new XRoute($this->app, $methods, $pattern, $callable);
    }


    public function redirect(string|UriInterface $from, $to, int $status = 302): void
    {
        $this->app->redirect($from, $to, $status);
    }
}

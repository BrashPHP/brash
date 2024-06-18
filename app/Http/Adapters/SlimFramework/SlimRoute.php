<?php
namespace Core\Http\Adapters\SlimFramework;

use Core\Http\Interfaces\RouteInterface;

class SlimRoute implements RouteInterface
{
    public function __construct(private \Slim\Interfaces\RouteInterface $route)
    {

    }
    public function add($middleware): RouteInterface
    {
        $this->route->add($middleware);

        return $this;
    }
    public function addMiddleware(\Psr\Http\Server\MiddlewareInterface $middleware): RouteInterface
    {
        $this->route->addMiddleware($middleware);

        return $this;
    }
}

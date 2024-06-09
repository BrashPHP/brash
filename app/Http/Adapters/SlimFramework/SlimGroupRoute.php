<?php
namespace Core\Http\Adapters\SlimFramework;

use Core\Http\Interfaces\GroupRouteInterface;
use Slim\Interfaces\RouteGroupInterface;

class SlimGroupRoute implements GroupRouteInterface
{
    public function __construct(public readonly RouteGroupInterface $group)
    {

    }
    public function add($middleware): self
    {
        $this->group->add($middleware);

        return $this;
    }
    public function addMiddleware(\Psr\Http\Server\MiddlewareInterface $middleware): self
    {
        $this->group->addMiddleware($middleware);

        return $this;
    }
    public function getPattern(): string
    {
        return $this->group->getPattern();
    }
}

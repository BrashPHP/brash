<?php
namespace Core\Http\Adapters\SlimFramework;

use Core\Http\Interfaces\MiddlewareAttachableInterface;
use Psr\Http\Server\MiddlewareInterface;

class SlimRoute implements MiddlewareAttachableInterface
{
    public function __construct(private \Slim\Interfaces\RouteInterface $route)
    {

    }
    public function add(MiddlewareInterface|string|callable ...$middleware): void
    {
        foreach ($middleware as $mid) {
            $this->route->add($mid);
        }
    }
}

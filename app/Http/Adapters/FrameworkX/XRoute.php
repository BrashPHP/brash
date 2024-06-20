<?php
namespace Core\Http\Adapters\FrameworkX;

use Core\Http\Interfaces\MiddlewareAttachableInterface;
use FrameworkX\App;
use Psr\Http\Server\MiddlewareInterface;

class XRoute implements MiddlewareAttachableInterface
{
    public function __construct(
        private App $route,
        private array $methods,
        private string $path,
        private \Closure|string $handler
    ) {
    }

    public function add(MiddlewareInterface|string|callable ...$middleware): void
    {
        $this->route->map($this->methods, $this->path, ...$middleware, $this->handler);
    }
}

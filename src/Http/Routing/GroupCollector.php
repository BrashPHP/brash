<?php

namespace Brash\Framework\Http\Routing;

use Brash\Framework\Http\Attributes\RouteGroup;
use Brash\Framework\Http\Domain\GroupModel;

final class GroupCollector
{
    public function getGroup(object|string $controller): GroupModel
    {
        $groupAttribute = $this->extractAttributeGroup($controller);

        if (is_null($groupAttribute)) {
            return new GroupModel('');
        }

        $path = '';
        $middlewares = new \SplStack;
        $shouldSkip = false;

        while ($groupAttribute) {
            if ($groupAttribute->skip) {
                $shouldSkip = true;
            }

            $path = implode('/', [trim(trim($groupAttribute->prefix, '/')), trim($path)]);
            if ($groupAttribute->middleware instanceof MiddlewareInterface || is_string($groupAttribute->middleware)) {
                $middlewares->push($groupAttribute->middleware);
            } elseif (is_array($groupAttribute->middleware)) {
                foreach ($groupAttribute->middleware as $middleware) {
                    $middlewares->push($middleware);
                }
            }

            $groupAttribute = $groupAttribute->parent ? $this->extractAttributeGroup($groupAttribute->parent) : null;
        }

        return new GroupModel($path, $middlewares, $shouldSkip);
    }

    private function extractAttributeGroup(object|string $controller): ?RouteGroup
    {
        $reflector = new \ReflectionClass($controller);

        $attrs = $reflector->getAttributes(RouteGroup::class);

        if ($attrs !== []) {
            return $attrs[0]->newInstance();
        }

        return null;
    }
}

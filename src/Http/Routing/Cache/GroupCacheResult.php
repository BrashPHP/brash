<?php

namespace Brash\Framework\Http\Routing\Cache;

use Brash\Framework\Http\Attributes\RouteGroup;
use Brash\Framework\Http\Domain\GroupModel;

class GroupCacheResult
{
    public function __construct(
        private array $cache = []
    ) {}

    private function forgeKey(string|RouteGroup $group): string
    {
        return is_object($group) ? $group->prefix : $group;
    }

    public function setCache(string|RouteGroup $group, GroupModel $value): self
    {
        $key = $this->forgeKey($group);

        $this->cache[$key] = $value;

        return $this;
    }

    public function get(string|object $group): ?GroupModel
    {
        $key = $this->forgeKey($group);

        return $this->cache[$key] ?? null;
    }
}

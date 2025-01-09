<?php

namespace Brash\Framework\Http\Domain;

readonly class GroupModel
{
    public function __construct(
        public string $prefix,
        /** @param \SplStack<string!object> $middlewares */
        public \SplStack $middlewares = new \SplStack,
        public bool $skip = false,
    ) {}
}

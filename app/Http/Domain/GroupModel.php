<?php

namespace Core\Http\Domain;

readonly class GroupModel
{
    public function __construct(
        public string $prefix,
        public array $middlewares = [],
        public bool $skip = false,
    ) {
    }
}

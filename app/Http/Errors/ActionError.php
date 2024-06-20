<?php

declare(strict_types=1);

namespace Core\Http\Errors;

use JsonSerializable;

readonly class ActionError implements JsonSerializable
{
    public function __construct(
        public string $type,
        public string $description
    ) {
    }

    /**
     * @return array
     */
    public function jsonSerialize(): mixed
    {
        return [
            'type' => $this->type,
            'description' => $this->description,
        ];
    }
}

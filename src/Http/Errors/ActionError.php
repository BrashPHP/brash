<?php

declare(strict_types=1);

namespace Brash\Framework\Http\Errors;

use JsonSerializable;

readonly class ActionError implements JsonSerializable
{
    public function __construct(
        public ErrorsEnum $type,
        public string $description
    ) {}

    /**
     * @return array
     */
    public function jsonSerialize(): mixed
    {
        return [
            'type' => $this->type->name,
            'description' => $this->description,
        ];
    }
}

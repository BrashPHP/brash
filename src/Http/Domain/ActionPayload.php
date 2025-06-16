<?php

declare(strict_types=1);

namespace Brash\Framework\Http\Domain;

use Brash\Framework\Http\Errors\ActionError;
use JsonSerializable;

class ActionPayload implements JsonSerializable
{
    public function __construct(
        private readonly int $statusCode = 200,
        private readonly array|object|null $data = null,
        private readonly ?ActionError $error = null
    ) {}

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getData(): object|array|null
    {
        return $this->data;
    }

    public function getError(): ?ActionError
    {
        return $this->error;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): mixed
    {
        $payload = [
            'statusCode' => $this->statusCode,
        ];

        if ($this->data !== null) {
            $payload['data'] = $this->data;
        } elseif ($this->error instanceof ActionError) {
            $payload['error'] = $this->error;
        }

        return $payload;
    }
}

<?php

declare(strict_types=1);

namespace Brash\Framework\Validation\Interfaces;

use Brash\Framework\Validation\ValidationExceptions\ValidationError;

abstract class AbstractValidator implements ValidationInterface
{
    protected string $field;

    protected ?string $message = null;

    public function validate(array $input): ?ValidationError
    {
        if (array_key_exists($this->field, $input)) {
            $subject = $input[$this->field];

            if ($this->makeValidation($subject)) {
                return null;
            }

            $message = $this->message ?? sprintf('%s does not match the defined requirements', $this->field);

            return $this->returnError(message: $message);
        }

        return $this->returnError(message: sprintf('%s is empty', $this->field));
    }

    abstract protected function makeValidation(mixed $subject): bool;

    private function returnError(string $message): ValidationError
    {
        return (
            new ValidationError(
                message: $message
            )
        )->forField($this->field);
    }
}

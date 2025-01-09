<?php

declare(strict_types=1);

namespace Brash\Framework\Validation\Factories;

use Closure;
use Brash\Framework\Validation\Adapters\AwesomeValidationAdapter;
use Brash\Framework\Validation\Adapters\CallbackValidationAdapter;
use Brash\Framework\Validation\Adapters\NestedValidationAdapter;
use Brash\Framework\Validation\Interfaces\AbstractValidator;
use Respect\Validation\Validatable;

class ValidatorFactory
{
    public function create(mixed $validation, string $key, string|array|null $message = null): ?AbstractValidator
    {
        if (is_array($validation)) {
            return $this->validationIsArray(
                $validation,
                $key,
                $message
            );
        }

        if ($validation instanceof Validatable) {
            return $this->validationIsAwesomeValidatable($validation, $key, $message);
        }

        if (is_callable($validation)) {
            return $this->validationIsACallable($validation, $key, $message);
        }

        return null;
    }

    private function validationIsACallable(
        callable $validation,
        string $key,
        string|array|null $message = null
    ): AbstractValidator {
        $closureValidation = Closure::fromCallable($validation);

        return new CallbackValidationAdapter($key, $closureValidation, $message);
    }

    private function validationIsArray(
        array $validation,
        string $key,
        string|array|null $message = null
    ): AbstractValidator {
        $nestedValidationAdapter = new NestedValidationAdapter($key);
        foreach ($validation as $key => $value) {
            $nestedMessage = $message;
            if (is_array($message)) {
                $nestedMessage = $message[$key] ?? null;
            }

            $nestedValidation = $this->create($value, $key, $nestedMessage);
            $nestedValidationAdapter->pushValidation($nestedValidation);
        }

        return $nestedValidationAdapter;
    }

    private function validationIsAwesomeValidatable(
        Validatable $validation,
        string $key,
        string|array|null $message = null
    ): AbstractValidator {
        return new AwesomeValidationAdapter($key, $validation, $message);
    }
}

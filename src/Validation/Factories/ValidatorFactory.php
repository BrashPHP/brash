<?php

declare(strict_types=1);

namespace Brash\Framework\Validation\Factories;

use Brash\Framework\Validation\Adapters\AwesomeValidationAdapter;
use Brash\Framework\Validation\Adapters\CallbackValidationAdapter;
use Brash\Framework\Validation\Adapters\NestedValidationAdapter;
use Brash\Framework\Validation\Interfaces\AbstractValidator;
use Closure;
use Respect\Validation\Validatable;

class ValidatorFactory
{
    public function create(mixed $validation, string $key, string|array|null $message = null): ?AbstractValidator
    {
        $mountedValidation = null;
        if (is_array($validation)) {
            $mountedValidation = $this->validationIsArray(
                $validation,
                $key,
                $message
            );
        } elseif ($validation instanceof Validatable) {
            $mountedValidation = $this->validationIsAwesomeValidatable($validation, $key, $message);
        } elseif (is_callable($validation)) {
            $mountedValidation = $this->validationIsACallable($validation, $key, $message);
        }

        return $mountedValidation;
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

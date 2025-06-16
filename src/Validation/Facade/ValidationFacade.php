<?php

declare(strict_types=1);

namespace Brash\Framework\Validation\Facade;

use Brash\Framework\Validation\Composite\Composite;
use Brash\Framework\Validation\Factories\ValidatorFactory;
use Brash\Framework\Validation\Interfaces\AbstractValidator;
use Brash\Framework\Validation\Interfaces\ValidationInterface;

class ValidationFacade
{
    public function __construct(private readonly array $rules, private array $messages = []) {}

    public function createValidations(): ValidationInterface
    {
        $composite = new Composite;
        $validatorFactory = new ValidatorFactory;

        foreach ($this->rules as $key => $validation) {
            $message = $this->messages[$key] ?? null;
            $validationRule = $validatorFactory->create($validation, $key, $message);

            if ($validationRule instanceof AbstractValidator) {
                $composite->pushValidation($validationRule);
            }
        }

        return $composite;
    }
}

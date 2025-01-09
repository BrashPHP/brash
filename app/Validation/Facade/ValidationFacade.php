<?php

declare(strict_types=1);

namespace Core\Validation\Facade;

use Core\Validation\Composite\Composite;
use Core\Validation\Factories\ValidatorFactory;
use Core\Validation\Interfaces\AbstractValidator;
use Core\Validation\Interfaces\ValidationInterface;

class ValidationFacade
{
    public function __construct(private array $rules, private array $messages = []) {}

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

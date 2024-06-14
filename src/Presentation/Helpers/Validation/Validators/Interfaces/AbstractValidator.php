<?php

namespace App\Presentation\Helpers\Validation\Validators\Interfaces;

use App\Presentation\Helpers\Validation\Validators\ValidationExceptions\ValidationError;

abstract class AbstractValidator implements ValidationInterface
{
    protected string $field;

    protected ?string $message;

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

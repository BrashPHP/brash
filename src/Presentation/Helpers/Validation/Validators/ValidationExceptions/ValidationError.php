<?php

namespace App\Presentation\Helpers\Validation\Validators\ValidationExceptions;

use Exception;

class ValidationError extends Exception
{
    private string $field = '';

    public function forField(string $field): self
    {
        $this->field = $field;

        return $this;
    }

    public function getField(): string
    {
        return $this->field;
    }
}

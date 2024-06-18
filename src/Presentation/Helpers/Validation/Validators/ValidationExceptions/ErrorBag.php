<?php

namespace App\Presentation\Helpers\Validation\Validators\ValidationExceptions;



class ErrorBag extends ValidationError
{
    public function __construct(
        public array $messages = [],
        public array $errors = []
    ) {
    }

    public function push(ValidationError $error): void
    {
        $this->errors[] = $error;
        $this->messages[] = [$error->getField() => $error->getMessage()];
        $this->message = json_encode($this->messages);
    }

    public function hasErrors(): bool
    {
        return $this->errors !== [];
    }
}

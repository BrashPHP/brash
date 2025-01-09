<?php

declare(strict_types=1);

namespace Core\Validation\Composite;

use Core\Validation\Interfaces\ValidationInterface;
use Core\Validation\ValidationExceptions\ErrorBag;
use Core\Validation\ValidationExceptions\ValidationError;

class Composite implements ValidationInterface
{
    public function __construct(
        /**
         * @var ValidationInterface[]
         */
        public array $compositions = [],
        public readonly ErrorBag $errorBag = new ErrorBag
    ) {}

    public function pushValidation(ValidationInterface $validation): self
    {
        $this->compositions[] = $validation;

        return $this;
    }

    public function validate(array $input): ?ValidationError
    {
        foreach ($this->compositions as $validation) {
            $error = $validation->validate($input);
            if ($error instanceof ValidationError) {
                $this->errorBag->push($error);
            }
        }

        return $this->errorBag->hasErrors() ? $this->errorBag : null;
    }

    public function getErrorBag(): ErrorBag
    {
        return $this->errorBag;
    }
}

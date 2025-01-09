<?php

declare(strict_types=1);

namespace Core\Validation\Adapters;

use Core\Validation\Interfaces\AbstractValidator;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validatable;

class AwesomeValidationAdapter extends AbstractValidator
{
    public function __construct(
        protected string $field,
        protected Validatable $rule,
        protected ?string $message = null
    ) {}

    protected function makeValidation(mixed $subject): bool
    {
        try {
            $this->rule->assert($subject);

            return true;
        } catch (NestedValidationException $nestedValidationException) {
            $this->message ??= $nestedValidationException->getFullMessage();

            return false;
        }
    }
}

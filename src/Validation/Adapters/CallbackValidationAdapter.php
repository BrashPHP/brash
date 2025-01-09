<?php

declare(strict_types=1);

namespace Brash\Framework\Validation\Adapters;

use Closure;
use Brash\Framework\Validation\Interfaces\AbstractValidator;

class CallbackValidationAdapter extends AbstractValidator
{
    public function __construct(
        protected string $field,
        protected Closure $rule,
        protected ?string $message = null
    ) {}

    protected function makeValidation(mixed $subject): bool
    {
        $rule = $this->rule;

        return $rule($subject);
    }
}

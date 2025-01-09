<?php

declare(strict_types=1);

namespace Brash\Framework\Validation\Interfaces;

use Brash\Framework\Validation\ValidationExceptions\ValidationError;

interface ValidationInterface
{
    public function validate(array $input): ?ValidationError;
}

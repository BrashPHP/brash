<?php

declare(strict_types=1);

namespace Core\Validation\Interfaces;

use Core\Validation\ValidationExceptions\ValidationError;

interface ValidationInterface
{
    public function validate(array $input): ?ValidationError;
}

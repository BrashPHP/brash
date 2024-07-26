<?php

declare(strict_types=1);

namespace Core\Validation\Adapters;


use Core\Validation\Composite\Composite;
use Core\Validation\Interfaces\AbstractValidator;
use Core\Validation\Interfaces\ValidationInterface;
use Core\Validation\ValidationExceptions\ErrorBag;
use Core\Validation\ValidationExceptions\ValidationError;


class NestedValidationAdapter extends AbstractValidator
{
    private Composite $composite;

    public function __construct(protected string $field)
    {
        $this->composite = new Composite();
        $this->message = sprintf('%s should be set as a dictionary or object', $this->field);
    }

    public function pushValidation(ValidationInterface $validation): void
    {
        $this->composite->pushValidation($validation);
    }

    public function validate(array $input): ?ValidationError
    {
        $error = parent::validate($input);
        if (is_null($error)) {
            $subject = $input[$this->field];
            $response = $this->composite->validate($subject);
            if ($response instanceof ValidationError) {

                $errors = $this->mapErrors(
                    $this->composite->errorBag->errors
                );
                $errorBag = new ErrorBag();
                foreach ($errors as $value) {
                      $errorBag->push($value);
                }

                return $errorBag->forField($this->field);
            }

            return null;
        }

        return $error;
    }

    protected function makeValidation(mixed $subject): bool
    {
        return isset($subject) && is_array($subject);
    }

    /**
     *
     * @param  ValidationError[] $validationErrors
     * @return ValidationError[]
     */
    private function mapErrors(array $validationErrors): array
    {
        return array_map(
            function (ValidationError $error) {
                $parentField = $this->field;

                $newError = new ValidationError(
                    $error->getMessage()
                );


                return $newError->forField(
                    sprintf('%s -> ', $parentField) . $error->getField()
                );
            }, $validationErrors
        );
    }
}

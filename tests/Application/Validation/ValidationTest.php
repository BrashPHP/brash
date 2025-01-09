<?php

namespace Tests\Application\Validation;

use Core\Validation\Facade\ValidationFacade;
use Core\Validation\ValidationExceptions\ErrorBag;
use Respect\Validation\Validator as v;

test('should validate incorrect values using ValidationFacade', function () {
    $factory = new ValidationFacade(['name' => fn ($value) => $value !== 'gabo']);

    /** @var ErrorBag */
    $result = $factory->createValidations()->validate(['name' => 'gabo']);

    expect($result)->toBeInstanceOf(ErrorBag::class);
    expect($result->messages)->toContainEqual(['name' => 'name does not match the defined requirements']);

});

test('should validate using Awesome Validation', function () {
    $factory = new ValidationFacade(['cpf' => v::cpf()]);

    /** @var ErrorBag */
    $result = $factory->createValidations()->validate(['cpf' => 'gabo']);

    expect($result)->toBeInstanceOf(ErrorBag::class);
    expect($result->messages)->toContainEqual(['cpf' => 'cpf does not match the defined requirements']);

});

test('should validate nested values', function () {
    $factory = new ValidationFacade(['object' => ['type' => fn ($value) => $value === 'car']]);

    /** @var ErrorBag */
    $result = $factory->createValidations()->validate(['object' => ['type' => '']]);

    expect($result)->toBeInstanceOf(ErrorBag::class);
    expect(json_decode($result->getMessage(), true))->toContainEqual([
        'object' => '[{"object -> type":"type does not match the defined requirements"}]',
    ]);

});

test('should validate correct values', function () {
    $factory = new ValidationFacade(['person' => fn ($value) => $value === 'optimusprime']);

    $result = $factory->createValidations()->validate(['person' => 'optimusprime']);

    expect($result)->toBeNull();
});

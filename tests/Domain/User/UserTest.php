<?php

declare(strict_types=1);

namespace Tests\Domain\User;

use App\Domain\Models\User;

dataset('userProvider', function () {
    return [
        [1, 'bill.gates', 'Bill', 'Gates'],
        [2, 'steve.jobs', 'Steve', 'Jobs'],
        [3, 'mark.zuckerberg', 'Mark', 'Zuckerberg'],
        [4, 'evan.spiegel', 'Evan', 'Spiegel'],
        [5, 'jack.dorsey', 'Jack', 'Dorsey'],
    ];
});

test('getters', function ($id, $username, $firstName, $lastName) {
    $user = new User($id, $username, $firstName, $lastName);

    expect($user->id)->toEqual($id);
    expect($user->username)->toEqual($username);
    expect($user->firstName)->toEqual($firstName);
    expect($user->lastName)->toEqual($lastName);
})->with('userProvider');

test('json serialize', function ($id, $username, $firstName, $lastName) {
    $user = new User($id, $username, $firstName, $lastName);

    $expectedPayload = json_encode(
        [
            'id' => $id,
            'username' => $username,
            'firstName' => $firstName,
            'lastName' => $lastName,
        ]
    );

    expect(json_encode($user))->toEqual($expectedPayload);
})->with('userProvider');

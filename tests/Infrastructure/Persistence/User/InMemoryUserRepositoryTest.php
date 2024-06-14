<?php

declare(strict_types=1);
use App\Domain\Exceptions\UserNotFoundException;
use App\Domain\Models\User;
use App\Infrastructure\Persistence\MemoryRepositories\InMemoryUserRepository;

test('find all', function () {
    $user = new User(1, 'bill.gates', 'Bill', 'Gates');

    $userRepository = new InMemoryUserRepository([1 => $user]);

    expect($userRepository->findAll())->toEqual([$user]);
});

test('find user of id', function () {
    $user = new User(1, 'bill.gates', 'Bill', 'Gates');

    $userRepository = new InMemoryUserRepository([1 => $user]);

    expect($userRepository->findUserOfId(1))->toEqual($user);
});

test('find user of id throws not found exception', function () {
    $userRepository = new InMemoryUserRepository([]);
    $this->expectException(UserNotFoundException::class);
    $userRepository->findUserOfId(1);
});

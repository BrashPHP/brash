<?php

declare(strict_types=1);

namespace Tests\Domain\UseCases\Auth;

use Mockery;
use Mockery\MockInterface;
use \Tests\Domain\UseCases\Auth\AuthSutTypes;
use App\Data\Protocols\Auth\LoginServiceInterface;
use App\Data\Protocols\Cryptography\ComparerInterface;
use App\Data\UseCases\Authentication\Errors\IncorrectPasswordException;
use App\Data\UseCases\Authentication\Login;
use App\Domain\Dto\Credentials;
use App\Domain\Dto\TokenLoginResponse;
use App\Domain\Exceptions\NoAccountFoundException;
use App\Domain\Models\Account;
use App\Domain\Repositories\AccountRepository;
use Ramsey\Uuid\Uuid;
use function PHPUnit\Framework\assertTrue;


beforeEach(function () {
    $this->sut = new AuthSutTypes(mockRepository(), makeComparer());
    $this->defaultUuid = '5a4bd710-aab8-4ebc-b65d-0c059a960cfb';
    $this->defaultMail = '@mail.com';
});

function makeCredentials()
{
    return new Credentials(access: '@mail.com', password: 'password');
}
/**
 * @param AccountRepository $repository
 * @param ComparerInterface $comparer
 */
function makeService($repository, $comparer): LoginServiceInterface
{
    return new Login($repository, $comparer);
}
function mockRepository(): AccountRepository|MockInterface
{
    return mock(AccountRepository::class);
}

function makeComparer(): MockInterface|ComparerInterface
{
    return Mockery::mock(ComparerInterface::class);
}

test('should call repository with correct email', function () {
    $mock = mockRepository();
    $comparer = makeComparer();
    $comparer->allows()->compare()->withAnyArgs()->andReturn(true);
    $sut = new AuthSutTypes($mock, $comparer);
    $loginService = $sut->service;
    $uuid = Uuid::fromString($this->defaultUuid);
    $account = new Account(null, $this->defaultMail, '', '', '', $uuid);
    $mock->expects('findByAccess')->once()->with($this->defaultMail)->andReturn($account);
    $accountStub = makeCredentials();
    $loginService->auth($accountStub);
});

test('should throw error if no account is found', function () {
    /** @var MockInterface */
    $mock = $this->sut->repository;
    $loginService = $this->sut->service;
    $mock->expects('findByAccess')->andReturn(null);
    $accountStub = makeCredentials();

    expect(fn() => $loginService->auth($accountStub))->toThrow(NoAccountFoundException::class);
});

test('should call hash comparer with correct values', function () {
    /** @var MockInterface */
    $mock = $this->sut->comparer;
    $credentialsStub = makeCredentials();
    $mock->shouldReceive('compare')
        ->withArgs(['password', 'hashed_password']);
    $repository = $this->sut->repository;
    $uuid = Uuid::fromString($this->defaultUuid);
    $account = new Account(id: 2, password: 'hashed_password', email: 'mail.com', username: 'user', authType: '', uuid: $uuid);
    $repository->shouldReceive('findByAccess')->andReturn(
        $account
    );
    $loginService = $this->sut->service;
    try {
        $loginService->auth($credentialsStub);
    } catch (\Throwable $th) {
        expect($th->getMessage())->toBe('');
    }
});

test('should throw if password differs from retrieved one', function () {
    $mockRepository = mockRepository();
    $uuid = Uuid::fromString($this->defaultUuid);
    $mockRepository->shouldReceive('findByAccess')->andReturn(
        new Account(
            2,
            uuid: $uuid,
            password: 'hashed_password',
            email: 'mail.com',
            username: 'user',
            authType: ''
        )
    );

    $this->expectException(IncorrectPasswordException::class);

    /** @var MockInterface|ComparerInterface */
    $mock = Mockery::mock(ComparerInterface::class);
    $mock->shouldReceive('compare')->andReturn(false);

    $repository = $mockRepository;

    $loginService = makeService($repository, $mock);

    $credentialsStub = makeCredentials();
    $loginService->auth($credentialsStub);

    expect(fn() => $loginService->auth($credentialsStub))->toThrow(IncorrectPasswordException::class);
});

test('success case', function () {
    $uuid = Uuid::fromString($this->defaultUuid);
    $account = new Account(2, password: 'hashed_password', email: 'mail.com', username: 'user', authType: '', uuid: $uuid);

    $this->sut->comparer->shouldReceive('compare')->andReturn(true);
    $this->sut->repository->shouldReceive('findByAccess')->andReturn(
        $account
    );


    $credentialsStub = makeCredentials();
    $response = $this->sut->service->auth($credentialsStub);

    assertTrue($response instanceof TokenLoginResponse);
});

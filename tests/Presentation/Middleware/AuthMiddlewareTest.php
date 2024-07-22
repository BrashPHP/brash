<?php
declare(strict_types=1);

namespace Tests\Presentation\Middleware;

use App\Domain\Dto\AccountDto;
use App\Domain\Repositories\AccountRepository;
use App\Infrastructure\Cryptography\BodyTokenCreator;
use App\Infrastructure\Persistence\MemoryRepositories\InMemoryAccountRepository;
use App\Presentation\Handlers\RefreshTokenHandler;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertSame;

beforeEach(function () {
    $this->app = $this->createAppInstance();
    $this->apiEndpoint = '/api/test-auth';
});

test('should pass when jwt is provided', function () {
    self::autowireContainer(AccountRepository::class, new InMemoryAccountRepository());

    $dto = new AccountDto(email: 'mail.com', username: 'user', password: 'pass');
    $repository = $this->getContainer()->get(AccountRepository::class);
    $account = $repository->insert($dto);

    $tokenCreator = new BodyTokenCreator($account);
    $token = $tokenCreator->createToken($_ENV['JWT_SECRET']);

    $bearer = 'Bearer ' . $token;

    $request = $this->createRequest(
        'GET',
        $this->apiEndpoint,
        [
            'HTTP_ACCEPT' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $bearer,
        ],
    );

    $response = $this->app->handle($request);

    assertNotNull($response);
    assertSame($response->getBody()->__toString(), "Works");
    assertSame(200, $response->getStatusCode());
});

test('should call error on jwterror handler when no refresh token is provided', function () {
    $response = $this->app->handle($this->createRequest('GET', $this->apiEndpoint));

    assertNotNull($response);
    assertSame(401, $response->getStatusCode());
});

test('should intercept http cookie refresh', function () {
    $request = $this->createRequest('GET', $this->apiEndpoint);

    $tokenValue = 'any_value';

    $request = $request->withCookieParams([REFRESH_TOKEN => $tokenValue]);

    $mockJwtRefreshHandler = mock(RefreshTokenHandler::class);

    $container = $this->getContainer();

    $container->set(RefreshTokenHandler::class, $mockJwtRefreshHandler);

    $response = $this->app->handle($request);

    assertNotNull($response);
});


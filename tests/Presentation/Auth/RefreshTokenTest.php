<?php

declare(strict_types=1);

namespace Tests\Presentation\Auth;

use App\Domain\Models\Account;
use App\Domain\Repositories\AccountRepository;
use App\Infrastructure\Cryptography\CookieTokenCreator;
use Firebase\JWT\JWT;
use Mockery\MockInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;

beforeEach(function () {
    $this->app = $this->createAppInstance();
    putenv('REFRESH_TOKEN_SECRET=cookieblabla');
    putenv('JWT_SECRET=blabla');
    $_ENV['REFRESH_TOKEN_SECRET'] = 'cookieblabla';
    $_ENV['JWT_SECRET'] = 'blabla';
});

test('should return401 for invalid cookie', function () {
    $response = $this
        ->app
        ->handle(createMockRequest($this));

    expect([
        'statusCode' => 401,
        'data' => [
            'status' => 'error',
            'message' => 'Cannot craft new token for invalid refresh token',
        ],
    ])->toBe(json_decode($response->getBody()->__toString(), true));
    expect(401)->toBe($response->getStatusCode());
});

test('should return401 for expired cookie', function () {
    $app = $this->app;
    $now = new \DateTime;
    $future = new \DateTime;
    $future->sub(new \DateInterval('P15D'));

    $jti = base64_encode(random_bytes(16)).$now->getTimeStamp();

    $payload = [
        'iat' => $now->getTimeStamp(),
        'exp' => $future->getTimeStamp(),
        'jti' => $jti,
        'sub' => Uuid::uuid4(),
        'iss' => 'ARTCHIE_COOKIE',
    ];

    $token = JWT::encode($payload, 'cookieblabla', 'HS256');

    $request = createMockRequest($this, [REFRESH_TOKEN => $token]);

    $response = $app->handle($request);

    expect([
        'statusCode' => 401,
        'data' => [
            'status' => 'error',
            'message' => 'Cannot craft new token for invalid refresh token',
        ],
    ])->toBe(json_decode($response->getBody()->__toString(), true));
    expect(401)->toBe($response->getStatusCode());
});
test('should return201 for valid cookie', function () {
    $app = $this->app;
    $repository = $this->getMockBuilder(AccountRepository::class)->getMock();
    $repository
        ->expects($this->once())
        ->method('findByUUID')
        ->willReturn(
            new Account(
                2,
                'mail2@mail.com',
                'mailusername2',
                'password2',
                'common',
            )
        );
    $this->autowireContainer(AccountRepository::class, $repository);
    $bodyTokenHandler = new CookieTokenCreator(Uuid::uuid4());
    $token = $bodyTokenHandler->createToken('cookieblabla');
    $request = createMockRequest($this, ['refresh' => $token])->withCookieParams([REFRESH_TOKEN => $token]);

    $response = $app->handle($request);

    $result = $response->getHeaders();

    expect($result)->toBeArray();
    expect($result)->not->toBeEmpty();
    expect(201)->toBe($response->getStatusCode());
});
function createMockRequest(\Tests\TestCase $app, array $cookies = []): ServerRequestInterface
{
    return $app->createRequest('GET', '/refresh-token', cookies: $cookies)
        ->withHeader(
            'Access-Control-Allow-Headers',
            'X-Requested-With, Content-Type, Accept, Origin, Authorization, X-Renew-Token'
        )
        ->withHeader('Access-Control-Expose-Headers', 'X-Renew-Token');
}

function createMockRepository(): AccountRepository|MockInterface
{
    /**
     * @var AccountRepository|MockInterface
     */
    $mock = mock(AccountRepository::class);

    $mock->shouldReceive('findByUUID')->andReturn(
        new Account(
            2,
            'mail2@mail.com',
            'mailusername2',
            'password2',
            'common',
        )
    );

    return $mock;
}

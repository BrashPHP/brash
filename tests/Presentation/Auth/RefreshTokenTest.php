<?php

declare(strict_types=1);

namespace Tests\Presentation\Auth;

use App\Domain\Models\Account;
use App\Domain\Repositories\AccountRepository;
use App\Infrastructure\Cryptography\CookieTokenCreator;
use DateInterval;
use DateTime;
use Firebase\JWT\JWT;
use Prophecy\Argument;
use Ramsey\Uuid\Uuid;
use PHPUnit\Framework\MockObject\MockObject;
use Prophecy\Prophet;
use Psr\Http\Message\ServerRequestInterface;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class RefreshTokenTest extends TestCase
{
    private Prophet $prophet;

    protected function setUp(): void
    {
        $this->app = $this->createAppInstance();
        putenv('JWT_SECRET_COOKIE=cookieblabla');
        putenv('JWT_SECRET=blabla');
        $_ENV["JWT_SECRET_COOKIE"] = "cookieblabla";
        $_ENV["JWT_SECRET"] = "blabla";
    }

    public function testShouldReturn401ForInvalidCookie()
    {
        $response = $this
            ->app
            ->handle($this->createMockRequest());

        $this->assertSame(json_decode($response->getBody()->__toString(), true), [
            'statusCode' => 401,
            'data' => [
                'status' => 'error',
                'message' => 'Cannot craft new token for invalid refresh token'
            ],
        ]);
        $this->assertSame($response->getStatusCode(), 401);
    }

    public function testShouldReturn401ForExpiredCookie()
    {
        $app = $this->app;
        $this->setUpErrorHandler($app);
        $now = new DateTime();
        $future = new DateTime();
        $future->sub(new DateInterval('P15D'));

        $jti = base64_encode(random_bytes(16)) . $now->getTimeStamp();

        $payload = [
            'iat' => $now->getTimeStamp(),
            'exp' => $future->getTimeStamp(),
            'jti' => $jti,
            'sub' => Uuid::uuid4(),
            'iss' => 'ARTCHIE_COOKIE',
        ];

        $token = JWT::encode($payload, 'cookieblabla', 'HS256');

        $request = $this->createMockRequest([REFRESH_TOKEN => $token]);

        $response = $app->handle($request);

        $this->assertSame(json_decode($response->getBody()->__toString(), true), [
            'statusCode' => 401,
            'data' => [
                'status' => 'error',
                'message' => 'Cannot craft new token for invalid refresh token'
            ],
        ]);
        $this->assertSame($response->getStatusCode(), 401);
    }

    public function testShouldReturn201ForValidCookie()
    {
        $app = $this->app;
        $this->setUpErrorHandler($app);
        $this->prophet = new Prophet();
        $repository = $this->prophet->prophesize()->willImplement(AccountRepository::class);
        $repository->findByUUID(Argument::any())
            ->willReturn(
                new Account(
                    2,
                    'mail2@mail.com',
                    'mailusername2',
                    'password2',
                    'common',
                )
            )
            ->shouldBeCalledOnce();
        $this->autowireContainer(AccountRepository::class, $repository->reveal());
        $bodyTokenHandler = new CookieTokenCreator(Uuid::uuid4());
        $token = $bodyTokenHandler->createToken('cookieblabla');
        $request = $this->createMockRequest(['refresh' => $token])->withCookieParams([REFRESH_TOKEN => $token]);

        $response = $app->handle($request);

        $result = $response->getHeaders();

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertSame($response->getStatusCode(), 201);

    }

    private function createMockRequest(array $cookies = []): ServerRequestInterface
    {
        $request = $this->createRequest('GET', '/auth/refresh-token', cookies: $cookies)
            ->withHeader(
                'Access-Control-Allow-Headers',
                'X-Requested-With, Content-Type, Accept, Origin, Authorization, X-Renew-Token'
            )
            ->withHeader('Access-Control-Expose-Headers', 'X-Renew-Token');

        return $request;
    }

    /**
     * Create a mocked login service.
     *
     * @return MockObject|AccountRepository
     */
    private function createMockRepository()
    {
        $mock = $this->getMockBuilder(AccountRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock->method('findByUUID')->willReturn(
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
}
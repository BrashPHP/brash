<?php

declare(strict_types=1);
use App\Domain\Dto\AccountDto;
use App\Infrastructure\Cryptography\BodyTokenCreator;
use App\Infrastructure\Persistence\MemoryRepositories\InMemoryAccountRepository;
use App\Presentation\Middleware\JWTAuthMiddleware;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertSame;

class RequestHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = new Response;
        $response->getBody()->write(json_encode($request->getAttribute('token')));

        return $response;
    }
}

beforeEach(function () {
    $container = $this->getContainer(true);
    $logger = $container->get(LoggerInterface::class);

    $this->sut = new JWTAuthMiddleware($logger);
});

test('should pass when jwt is provided and return token in attributes', function () {
    $dto = new AccountDto(email: 'mail.com', username: 'user', password: 'pass');
    $repository = new InMemoryAccountRepository;
    $account = $repository->insert($dto);

    $tokenCreator = new BodyTokenCreator($account);
    $token = $tokenCreator->createToken($_ENV['JWT_SECRET']);
    $request = createRequestWithAuthentication($this, $token);
    $response = $this->sut->process(
        $request,
        new RequestHandler
    );

    assertNotNull($response);
    $decoded = json_decode($response->getBody()->__toString());
    expect('common')->toBe($decoded->data->role);
    assertSame(200, $response->getStatusCode());
});
function createRequestWithAuthentication(\Tests\TestCase $app, string $token)
{
    $bearer = 'Bearer '.$token;

    return $app->createRequest(
        'GET',
        '/api/test-auth',
        [
            'HTTP_ACCEPT' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => $bearer,
        ],
    );
}

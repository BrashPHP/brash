<?php

declare(strict_types=1);

namespace App\Presentation\Actions\Auth;

use App\Data\Protocols\Auth\LoginServiceInterface;
use App\Domain\Dto\Credentials;
use App\Domain\Dto\TokenLoginResponse;
use App\Presentation\Actions\Auth\Utilities\CookieTokenManager;
use Core\Http\Action;
use Core\Http\Interfaces\ValidationInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use Respect\Validation\Validator;

use Core\Http\Attributes\Route;

#[Route(path: "login", method: "POST")]
class LoginController extends Action implements ValidationInterface
{
    public function __construct(
        private LoginServiceInterface $loginService,
        protected LoggerInterface $logger
    ) {
    }

    public function action(Request $request): PromiseInterface|Response
    {
        $this->logger->info('Start new login');
        $parsedBody = $request->getParsedBody();
        [
            'access' => $access,
            'password' => $password
        ] = $parsedBody;

        $this->logger->info('Received value login', $parsedBody);
        $credentials = new Credentials($access, $password);

        $promise = new Promise(function ($resolve) use ($credentials) {
            $result = $this->loginService->auth($credentials);
            $resolve($result);
        });
        $logger = $this->logger;
        $cookieTokenManager = new CookieTokenManager();

        return $promise->then(static function (TokenLoginResponse $token) use ($logger) {
            $refreshToken = $token->renewToken;

            $logger->info('Successfully implanted token', [$refreshToken]);

            return $token;
        })->then(
                fn(TokenLoginResponse $token) => [
                    $this
                        ->respondWithData(['token' => $token->token])
                        ->withStatus(201, 'Created token'),
                    $token->renewToken
                ]
            )
            ->then(
                fn(array $response) => $cookieTokenManager->appendCookieHeader(...$response)
            );
    }

    public function messages(): ?array
    {
        return [
            'access' => 'Email or username is not valid',
            'password' => 'Password wrong my dude',
        ];
    }

    public function rules(Request $request): ?array
    {
        return [
            'access' => Validator::anyOf(
                Validator::email(),
                Validator::alnum()->noWhitespace()->length(6, 20)
            ),
            'password' => static function ($value): bool {
                return (bool) preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[\d])[\w$@]{6,}$/m', $value);
            },
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Presentation\Actions\Auth;

use App\Data\Protocols\Auth\LoginServiceInterface;
use App\Domain\Dto\Credentials;
use App\Presentation\Actions\Auth\Utilities\CookieTokenManager;
use App\Presentation\Actions\Protocols\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Respect\Validation\Validator;

class LoginController extends Action
{

    public function __construct(
        private LoginServiceInterface $loginService,
        protected LoggerInterface $logger
    ) {
    }

    public function action(Request $request): Response
    {
        $this->logger->info('Start new login');
        $parsedBody = $request->getParsedBody();
        [
            'access' => $access,
            'password' => $password
        ] = $parsedBody;

        $body = print_r($parsedBody, true);
        $this->logger->info("Received value {$body} for input login");
        $credentials = new Credentials($access, $password);
        $tokenize = $this->loginService->auth($credentials);
        $refreshToken = $tokenize->renewToken;
        $cookieTokenManager = new CookieTokenManager();

        $this->logger->info("Successfully implanted token {$refreshToken}");

        $response = $this
            ->respondWithData(['token' => $tokenize->token])
            ->withStatus(201, 'Created token');

        return $cookieTokenManager->appendCookieHeader($response, $refreshToken);
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
            'password' => static function ($value) {
                return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[\w$@]{6,}$/m', $value);
            },
        ];
    }
}
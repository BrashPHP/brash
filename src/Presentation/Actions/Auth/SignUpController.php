<?php

declare(strict_types=1);

namespace App\Presentation\Actions\Auth;

use App\Data\Protocols\Auth\SignUpServiceInterface;
use App\Data\Protocols\Cryptography\HasherInterface;
use App\Domain\Dto\AccountDto;
use App\Presentation\Actions\Auth\Utilities\CookieTokenManager;
use Core\Http\Action;
use Core\Http\Attributes\Route;
use Core\Http\Interfaces\ValidationInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Respect\Validation\Validator;

#[Route(path: 'signup', method: 'POST')]
class SignUpController extends Action implements ValidationInterface
{
    private CookieTokenManager $cookieManager;

    public function __construct(
        private SignUpServiceInterface $service,
        private HasherInterface $hasherInterface,
        protected LoggerInterface $logger
    ) {
        $this->cookieManager = new CookieTokenManager;
    }

    public function action(Request $request): Response
    {
        $parsedBody = $request->getParsedBody();

        [
            'email' => $email,
            'username' => $username,
            'password' => $password,
        ] = $parsedBody;

        $this->logger->info(print_r($parsedBody, true));

        $password = $this->hasherInterface->hash($password);
        $account = new AccountDto(email: $email, username: $username, password: $password);
        $tokenize = $this->service->register($account);
        $refreshToken = $tokenize->renewToken;

        $response = $this
            ->respondWithData(['token' => $tokenize->token])
            ->withStatus(201, 'Created token');

        return $this->cookieManager->appendCookieHeader($response, $refreshToken);
    }

    public function messages(): ?array
    {
        return [
            'email' => 'Email not valid',
            'username' => 'A valid username must be provided',
            'password' => 'Password must contain at least 6 characters with at least one uppercase letter, one lower case letter and a symbol',
            'passwordConfirmation' => "Password confirmation doesn't match.",
        ];
    }

    /**
     * Summary of rules
     */
    public function rules(Request $request): ?array
    {
        $parsedBody = $request->getParsedBody();
        $password = $parsedBody['password'] ?? '';

        return [
            'email' => Validator::email(),
            'username' => Validator::alnum()->noWhitespace()->length(6, 20),
            'password' => static function ($value): bool {
                return (bool) preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[\d])[\w$@]{6,}$/m', $value);
            },
            'passwordConfirmation' => static fn ($value) => $value === $password,
        ];
    }
}

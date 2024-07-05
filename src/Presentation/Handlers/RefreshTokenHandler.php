<?php

namespace App\Presentation\Handlers;

use App\Domain\OptionalApi\Result;
use App\Domain\OptionalApi\Result\Err;
use App\Domain\OptionalApi\Result\Ok;
use App\Domain\Repositories\AccountRepository;
use App\Infrastructure\Cryptography\BodyTokenCreator;
use Core\Http\Exceptions\UnauthorizedException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Throwable;
use UnexpectedValueException;

/**
 * This class is called once the validation of a user's JWT throws invalid.
 * Then, this class instance is responsible to verify the user's refresh token existence and
 * forge a new JWT in case the Refresh Token exists.
 */
class RefreshTokenHandler
{
    public function __construct(
        private AccountRepository $repository
    ) {
    }

    /**
     * @return \App\Domain\OptionalApi\Result<string,UnauthorizedException>
     */
    public function refresh(
        string $refreshToken,
        string $secretBody,
        string $secretCookie
    ): Result {
        try {
            $key = new Key($secretCookie, 'HS256');
            $payload = JWT::decode($refreshToken, $key);
            $uuid = $payload->sub;
            $user = $this->repository->findByUUID($uuid);
            if ($user instanceof \App\Domain\Models\Account) {
                $tokenCreator = new BodyTokenCreator($user);
                return new Ok($tokenCreator->createToken($secretBody));
            }

            throw new UserDoesNotExistException();
        } catch (ExpiredException | UnexpectedValueException $exception) {
            $message = 'Cannot craft new token for invalid refresh token';

            return new Err(
                new UnauthorizedException(
                    $message,
                    code: 401,
                    previous: $exception
                )
            );
        } catch (Throwable $exception) {
            $message = 'You are not logged to access this resource';

            return new Err(
                new UnauthorizedException(
                    $message,
                    code: 401,
                    previous: $exception
                )
            );
        }
    }
}

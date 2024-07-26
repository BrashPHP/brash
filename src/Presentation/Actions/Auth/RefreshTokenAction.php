<?php

namespace App\Presentation\Actions\Auth;

use Core\Http\Action;
use App\Presentation\Handlers\RefreshTokenHandler;
use Core\Http\Attributes\Route;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

#[Route(path: "refresh-token", method: "GET")]
class RefreshTokenAction extends Action
{
    public function __construct(
        private RefreshTokenHandler $refreshTokenHandler,
        protected LoggerInterface $logger
    ) {
    }

    public function action(Request $request): Response
    {
        $secretBody = $_ENV["JWT_SECRET"];
        $secretCookie = $_ENV["REFRESH_TOKEN_SECRET"];
        $cookies = $request->getCookieParams();

        $cookieName = REFRESH_TOKEN;

        $refreshToken = $cookies[$cookieName] ?? "";

        $this->logger->info(sprintf('Attempt to renew token %s', $refreshToken));

        return $this->refreshTokenHandler
            ->refresh($refreshToken, $secretBody, $secretCookie)
            ->map(
                function (string $token) {
                    return $this->respondWithData(
                        [
                            "status" => "Success",
                            "message" => "Token successfully created",
                        ]
                    )
                        ->withHeader("X-RENEW-TOKEN", $token)
                        ->withStatus(201);
                }
            )
            ->unwrapOrElse(
                function (\Exception $exception) {
                    $this->logger->warning(
                        $exception->getPrevious()->getMessage()
                    );
                    return $this->respondWithData(
                        [
                            "status" => "error",
                            "message" => $exception->getMessage(),
                        ],
                        401
                    )->withStatus(401);
                }
            );
    }
}

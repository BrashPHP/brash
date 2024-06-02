<?php

namespace App\Presentation\Actions\Auth;

use App\Presentation\Actions\Protocols\Action;
use App\Presentation\Handlers\RefreshTokenHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

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
        $secretCookie = $_ENV["JWT_SECRET_COOKIE"];
        $cookies = $request->getCookieParams();

        $cookieName = REFRESH_TOKEN;

        $refreshToken = $cookies[$cookieName] ?? "";

        $this->logger->info("Attempt to renew token {$refreshToken}");

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
                        ], 401
                    )->withStatus(401);
                }
            );
    }
}

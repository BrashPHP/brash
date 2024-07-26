<?php

declare(strict_types=1);

namespace App\Presentation\Middleware;

use App\Infrastructure\Cryptography\Exceptions\AppHasNoDefinedSecrets;
use Core\Http\Middlewares\Jwt\JwtAuthentication\JwtAuthOptions;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Core\Http\Middlewares\Jwt\JwtAuthentication;
use Psr\Log\LoggerInterface;

class JWTAuthMiddleware implements Middleware
{
    public function __construct(private LoggerInterface $logger)
    {
        $shouldHave = ["JWT_SECRET", "REFRESH_TOKEN_SECRET"];

        foreach ($shouldHave as $field) {
            if (!array_key_exists($field, $_ENV)) {
                throw new AppHasNoDefinedSecrets($field);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $secret = $_ENV["JWT_SECRET"];

        $jwtAuth = new JwtAuthentication(
            new JwtAuthOptions(
                secret: $secret,
                path: ["/api"],
                ignore: ["/api/auth", "/admin/ping"],
                relaxed: ["localhost", "dev.example.com"],
                secure: false,
                error: $this->onError()
            ),
            $this->logger
        );

        return $jwtAuth->process($request, $handler);
    }

    private function onError(): \Closure
    {
        return function (Response $response) : Response {
            $response = $response->withHeader('Content-Type', 'application/json');
            $response
                ->getBody()
                ->write(
                    json_encode(
                        [
                        "message" =>
                            "You are not allowed to acess this resource",
                        ]
                    )
                );
            return $response;
        };
    }
}

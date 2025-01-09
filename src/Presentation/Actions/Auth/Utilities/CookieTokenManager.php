<?php

namespace App\Presentation\Actions\Auth\Utilities;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Cookies;

use function Core\functions\isProd;

class CookieTokenManager
{
    public function appendCookieHeader(Response $response, string $refreshToken): Response
    {
        $cookie = new Cookies;
        $values = $this->arrayValues();
        $values['value'] = $refreshToken;

        $cookie->set(REFRESH_TOKEN, $values);

        foreach ($cookie->toHeaders() as $header) {
            $response = $response->withAddedHeader('Set-Cookie', $header);
        }

        return $response;
    }

    public function delete(Response $response): Response
    {
        $options = $this->arrayValues();
        $options['expires'] = time() - 3600;
        $cookie = new Cookies;
        $cookie->set(REFRESH_TOKEN, $options);

        foreach ($cookie->toHeaders() as $header) {
            $response = $response->withAddedHeader('Set-Cookie', $header);
        }

        return $response;
    }

    private function arrayValues(): array
    {
        $options = [
            'expires' => time() + 31536000,
            'httponly' => 'true',
            // 'samesite' => $sameSite,
            'path' => '/',
        ];

        if (isProd()) {
            $options['samesite'] = 'Strict';
        }

        return $options;
    }
}

<?php

namespace App\Presentation\Actions\Auth\Utilities;

use function Core\functions\isProd;

class CookieTokenManager
{
    public function implant(string $refreshToken)
    {
        setcookie(
            REFRESH_TOKEN,
            $refreshToken,
            $this->mountOptions()
        );
    }

    public function delete()
    {
        $options = $this->mountOptions();
        $time = new \DateTimeImmutable();
        $time = $time->sub(new \DateInterval('PT1H'));
        $options['expires'] = $time->format(\DateTime::COOKIE);
        dd($options);
        setcookie(
            REFRESH_TOKEN,
            '',
            $options
        );
    }

    private function mountOptions(): array
    {
        $isProd = isProd();
        $sameSite = $isProd ? 'None' : '';
        $secure = $isProd;
        $time = new \DateTimeImmutable();
        $time = $time->add(new \DateInterval('P15D'));

        return [
            'expires' => $time->format(\DateTime::COOKIE),
            'path' => '/',
            'httponly' => true,
            'samesite' => $sameSite,
            'secure' => $secure,
        ];
    }
}
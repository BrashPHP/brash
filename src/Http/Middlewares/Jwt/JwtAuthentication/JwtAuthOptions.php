<?php

declare(strict_types=1);

namespace Brash\Framework\Http\Middlewares\Jwt\JwtAuthentication;

use Brash\Framework\Http\Middlewares\Jwt\JwtAuthentication;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class JwtAuthOptions
{
    public array $secret;

    public array $algorithm;

    public ?\Closure $before;

    public ?\Closure $after;

    public ?\Closure $error;

    private JwtAuthentication $jwtAuthentication;

    public function __construct(
        string|array $secret,
        string|array $algorithm = 'HS256',
        public bool $secure = true,
        public array $relaxed = ['localhost', '127.0.0.1'],
        public string $header = 'Authorization',
        public string $regexp = "/Bearer\s+(.*)$/i",
        public string $cookie = 'token',
        public string $attribute = 'token',
        public array $path = ['/'],
        public array $ignore = [],
        public array $rules = [],
        ?callable $before = null,
        ?callable $after = null,
        ?callable $error = null
    ) {
        $this->secret = $this->validateSecret($secret);
        $this->algorithm = $this->assignAlgorithm($this->secret, $algorithm);
        $this->before = $this->convertToClosure($before);
        $this->after = $this->convertToClosure($after);
        $this->error = $this->convertToClosure($error);
    }

    public static function fromArray(array $data): self
    {
        $defaults = [
            'secret' => '',
            'algorithm' => 'HS256',
            'secure' => true,
            'relaxed' => ['localhost', '127.0.0.1'],
            'header' => 'Authorization',
            'regexp' => "/Bearer\s+(.*)$/i",
            'cookie' => 'token',
            'attribute' => 'token',
            'path' => ['/'],
            'ignore' => [],
            'rules' => [],
            'before' => null,
            'after' => null,
            'error' => null,
        ];

        return new self(...array_merge($defaults, $data));
    }

    public function bindToAuthentication(JwtAuthentication $auth): self
    {
        $this->jwtAuthentication = $auth;

        return $this;
    }

    public function onError(ResponseInterface $response, array $arguments): ?ResponseInterface
    {
        return $this->error?->call($this->jwtAuthentication, $response, $arguments);
    }

    public function onBeforeCallable(ServerRequestInterface $request, array $params): ?ServerRequestInterface
    {
        return $this->before?->call($this->jwtAuthentication, $request, $params);
    }

    public function onAfterCallable(ResponseInterface $response, array $params): ?ResponseInterface
    {
        return $this->after?->call($this->jwtAuthentication, $response, $params);
    }

    private function validateSecret(string|array $secret): array
    {
        if (! is_string($secret) && ! is_array($secret)) {
            throw new InvalidArgumentException(
                'Secret must be a string or an array of "kid" => "secret" pairs.'
            );
        }

        return (array) $secret;
    }

    private function assignAlgorithm(array $secret, string|array $algorithm): array
    {
        if (is_string($algorithm)) {
            return array_fill_keys(array_keys($secret), $algorithm);
        }

        foreach (array_keys($secret) as $key) {
            if (! array_key_exists($key, $algorithm)) {
                throw new InvalidArgumentException(
                    'Each secret must have a corresponding algorithm.'
                );
            }
        }

        return $algorithm;
    }

    private function convertToClosure(?callable $callback): ?\Closure
    {
        if ($callback instanceof \Closure || $callback === null) {
            return $callback;
        }

        return \Closure::fromCallable($callback);
    }
}

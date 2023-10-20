<?php

declare(strict_types=1);

namespace Core\Http\Middlewares\Jwt\JwtAuthentication;

use Core\Http\Middlewares\Jwt\JwtAuthentication;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * This class stores all the options passed to the middleware.
 */
class JwtAuthOptions
{
    public string $secret;

    public bool $secure;

    /** @var array<string> */
    public array $relaxed;
    public string $algorithm;
    public string $header;
    public string $regexp;
    public string $cookie;
    public string $attribute;
    /** @var array<string> */
    public array $path;

    /** @var RuleInterface[] $rules */
    public array $rules;

    /** @var array<string> */
    public array $ignore;
    public $before;
    public $after;
    public $error;

    private JwtAuthentication $jwtAuthentication;

    public function __construct(
        string|array $secret,
        bool $secure = true,
        array $relaxed = ["localhost", "127.0.0.1"],
        string $algorithm = "HS256",
        string $header = "Authorization",
        string $regexp = "/Bearer\s+(.*)$/i",
        string $cookie = "token",
        string $attribute = "token",
        array $path = ["/"],
        array $ignore = [],
        array $rules = [],
        ?callable $before = null,
        ?callable $after = null,
        ?callable $error = null
    ) {
        $this->secret = $this->checkSecret($secret);
        $this->secure = $secure;
        $this->relaxed = $relaxed;
        $this->algorithm = $algorithm;
        $this->header = $header;
        $this->regexp = $regexp;
        $this->cookie = $cookie;
        $this->attribute = $attribute;
        $this->path = $path;
        $this->rules = $rules;
        $this->ignore = $ignore;
        $this->before = $before;
        $this->after = $after;
        $this->error = $error;
    }

    private function checkSecret($secret): string|array
    {
        if (!(is_array($secret) || is_string($secret) || $secret instanceof \ArrayAccess)) {
            throw new InvalidArgumentException(
                'Secret must be either a string or an array of "kid" => "secret" pairs'
            );
        }
        return $secret;
    }

    private function bindClosure(?callable $closure, JwtAuthentication $target): ?\Closure
    {
        if ($closure) {
            if ($closure instanceof \Closure) {
                return $closure->bindTo($target);
            }

            return \Closure::fromCallable($closure);
        }

        return null;
    }

    public function bindToAuthentication(JwtAuthentication $target): self
    {
        $this->jwtAuthentication = $target;

        $this->error = $this->bindClosure($this->error, $target);
        $this->before = $this->bindClosure($this->before, $target);
        $this->after = $this->bindClosure($this->after, $target);

        return $this;
    }

    /**
     * Set the error handler.
     */
    public function onError(ResponseInterface $response, array $arguments): ?ResponseInterface
    {
        $func = $this->error;

        return is_null($func) ? null : $func($response, $arguments);
    }

    /**
     * Set the before handler.
     */

    public function onBeforeCallable(ServerRequestInterface $request, array $params): ?ServerRequestInterface
    {
        $func = $this->before;

        return is_null($func) ? null : $func($request, $params);
    }
    /**
     * Set the after handler.
     */
    public function onAfterCallable(ResponseInterface $response, array $params): ?ResponseInterface
    {
        $func = $this->after;

        return is_null($func) ? null : $func($response, $params);
    }
}

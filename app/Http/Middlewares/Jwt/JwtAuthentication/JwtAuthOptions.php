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
    public array $secret;
    public array $algorithm;
    public bool $secure;

    /**
     * @var array<string> 
     */
    public array $relaxed;
    public string $header;
    public string $regexp;
    public string $cookie;
    public string $attribute;
    /**
     * @var array<string> 
     */
    public array $path;

    /**
     * @var RuleInterface[] $rules 
     */
    public array $rules;

    /**
     * @var array<string> 
     */
    public array $ignore;
    public $before;
    public $after;
    public $error;

    private JwtAuthentication $jwtAuthentication;

    public function __construct(
        string|array $secret,
        string|array $algorithm = "HS256",
        bool $secure = true,
        array $relaxed = ["localhost", "127.0.0.1"],
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
        $this->algorithm = $this->applyAlgorithm($this->secret, $algorithm);
        $this->secure = $secure;
        $this->relaxed = $relaxed;
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

    public static function fromArray(array $data): self
    {
        $values = [
            "secret" => "",
            "algorithm" => "HS256",
            "secure" => true,
            "relaxed" => ["localhost", "127.0.0.1"],
            "header" => "Authorization",
            "regexp" => "/Bearer\s+(.*)$/i",
            "cookie" => "token",
            "attribute" => "token",
            "path" => ["/"],
            "ignore" => [],
            "rules" => [],
            "before" => null,
            "after" => null,
            "error" => null
        ];
        $inArray = [];

        foreach ($values as $key => $value) {
            $inArray[$key] = $data[$key] ?? $value;
        }

        return new self(...$inArray);
    }

    public function bindToAuthentication(JwtAuthentication $target): self
    {
        $this->jwtAuthentication = $target;

        $this->error = $this->bindClosure($this->error, $target);
        $this->before = $this->bindClosure($this->before, $target);
        $this->after = $this->bindClosure($this->after, $target);

        return $this;
    }

    public function onError(ResponseInterface $response, array $arguments): ?ResponseInterface
    {
        $func = $this->error;

        return is_null($func) ? null : $func($response, $arguments);
    }

    public function onBeforeCallable(ServerRequestInterface $request, array $params): ?ServerRequestInterface
    {
        $func = $this->before;

        return is_null($func) ? null : $func($request, $params);
    }


    public function onAfterCallable(ResponseInterface $response, array $params): ?ResponseInterface
    {
        $func = $this->after;

        return is_null($func) ? null : $func($response, $params);
    }

    private function checkSecret($secret): array
    {
        if (!(is_array($secret) || is_string($secret) || $secret instanceof \ArrayAccess)) {
            throw new InvalidArgumentException(
                'Secret must be either a string or an array of "kid" => "secret" pairs'
            );
        }

        return (array) $secret;
    }

    private function applyAlgorithm(array $secret, $algorithm)
    {
        if (is_string($algorithm)) {
            $secretIndex = array_keys($secret);

            return array_fill_keys($secretIndex, $algorithm);
        }

        foreach ($secret as $key => $value) {
            if (!in_array($key, $algorithm)) {
                throw new InvalidArgumentException(
                    "All secrets must have a corresponding algorithm"
                );
            }
        }

        return $algorithm;
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
}

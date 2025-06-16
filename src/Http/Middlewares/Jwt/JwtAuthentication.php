<?php

declare(strict_types=1);

namespace Brash\Framework\Http\Middlewares\Jwt;

use Brash\Framework\Http\Middlewares\DoublePass\DoublePassTrait;
use Brash\Framework\Http\Middlewares\Jwt\Exceptions\InsecureUseOfMiddlewareException;
use Brash\Framework\Http\Middlewares\Jwt\Exceptions\TokenNotFoundException;
use Brash\Framework\Http\Middlewares\Jwt\JwtAuthentication\JwtAuthOptions;
use Brash\Framework\Http\Middlewares\Jwt\JwtAuthentication\RequestMethodRule;
use Brash\Framework\Http\Middlewares\Jwt\JwtAuthentication\RequestPathRule;
use Brash\Framework\Http\Middlewares\Jwt\JwtAuthentication\RuleInterface;
use DomainException;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use RuntimeException;
use SplStack;

final class JwtAuthentication implements MiddlewareInterface
{
    use DoublePassTrait;

    /**
     * The rules stack.
     *
     * @var SplStack<RuleInterface>
     */
    private SplStack $rules;

    private JwtAuthOptions $options;

    private ResponseFactoryInterface $responseFactory;

    public function __construct(
        JwtAuthOptions $options,
        /**
         * PSR-3 compliant logger.
         */
        private ?LoggerInterface $logger = null,
        ?ResponseFactoryInterface $responseFactoryInterface = null
    ) {
        /* Setup stack for rules */
        $this->rules = new SplStack;

        $this->options = $options->bindToAuthentication($this);

        $this->responseFactory = $responseFactoryInterface ?? new Psr17Factory;

        /* If nothing was passed in options add default rules. */
        /* This also means $options->rules overrides $options->path */
        /* and $options->ignore */
        if ($options->rules === []) {
            $this->rules->push(
                new RequestMethodRule(
                    [
                        'ignore' => ['OPTIONS'],
                    ]
                )
            );
            $this->rules->push(
                new RequestPathRule(
                    [
                        'path' => $this->options->path,
                        'ignore' => $this->options->ignore,
                    ]
                )
            );
        } else {
            $this->rules($options->rules);
        }
    }

    /**
     * Process a request in PSR-15 style and return a response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $scheme = $request->getUri()->getScheme();
        $host = $request->getUri()->getHost();

        /* If rules say we should not authenticate call next and return. */
        if ($this->shouldAuthenticate($request) === false) {
            return $handler->handle($request);
        }

        /* HTTP allowed only if secure is false or server is in relaxed array. */
        if ($scheme !== 'https' && $this->options->secure && ! in_array($host, $this->options->relaxed)) {

            throw new InsecureUseOfMiddlewareException($scheme);
        }

        /* If token cannot be found or decoded return with 401 Unauthorized. */
        try {
            $token = $this->fetchToken($request);
            $decoded = $this->decodeToken($token);
        } catch (RuntimeException|DomainException $exception) {
            $response = $this->responseFactory->createResponse(401);

            return $this->processError(
                $response,
                [
                    'message' => $exception->getMessage(),
                    'uri' => (string) $request->getUri(),
                ]
            );
        }

        $params = [
            'decoded' => $decoded,
            'token' => $token,
        ];

        /* Add decoded token to request as attribute when requested. */
        if ($this->options->attribute !== '' && $this->options->attribute !== '0') {
            $request = $request->withAttribute($this->options->attribute, $decoded);
        }

        /* Modify $request before calling next middleware. */
        $beforeRequest = $this->options->onBeforeCallable($request, $params);

        if ($beforeRequest instanceof ServerRequestInterface) {
            $request = $beforeRequest;
        }

        /* Everything ok, call next middleware. */
        $response = $handler->handle($request);

        /* Modify $response before returning. */
        $afterResponse = $this->options->onAfterCallable($response, $params);

        return $afterResponse ?? $response;
    }

    /**
     * Set all rules in the stack.
     *
     * @param  RuleInterface[]  $rules
     */
    public function withRules(array $rules): self
    {
        $new = clone $this;
        /* Clear the stack */
        unset($new->rules);
        $new->rules = new SplStack;
        /* Add the rules */
        foreach ($rules as $callable) {
            $new = $new->addRule($callable);
        }

        return $new;
    }

    /**
     * Add a rule to the stack.
     */
    public function addRule(callable $callable): self
    {
        $new = clone $this;
        $new->rules = clone $this->rules;
        $new->rules->push($callable);

        return $new;
    }

    /**
     * Check if middleware should authenticate.
     */
    private function shouldAuthenticate(ServerRequestInterface $request): bool
    {
        /* If any of the rules in stack return false will not authenticate */
        foreach ($this->rules as $callable) {
            if (! $callable($request)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Call the error handler if it exists.
     *
     * @param  mixed[]  $arguments
     */
    private function processError(ResponseInterface $response, array $arguments): ResponseInterface
    {
        return $this->options->onError($response, $arguments) ?? $response;
    }

    /**
     * Fetch the access token.
     */
    private function fetchToken(ServerRequestInterface $request): string
    {
        /* Check for token in header. */
        $header = $request->getHeaderLine($this->options->header);

        if (! empty($header) && preg_match($this->options->regexp, $header, $matches)) {
            $this->log(LogLevel::DEBUG, 'Using token from request header');

            return $matches[1];
        }

        /* Token not found in header try a cookie. */
        $cookieParams = $request->getCookieParams();

        if (isset($cookieParams[$this->options->cookie])) {
            $this->log(LogLevel::DEBUG, 'Using token from cookie');
            if (preg_match($this->options->regexp, (string) $cookieParams[$this->options->cookie], $matches)) {
                return $matches[1];
            }

            return $cookieParams[$this->options->cookie];
        }

        /* If everything fails log and throw. */
        $this->log(LogLevel::WARNING, 'Token not found');

        throw new TokenNotFoundException;
    }

    private function decodeToken(string $token): array
    {
        $keys = $this->createKeysFromAlgorithms();

        if (count($keys) === 1) {
            $keys = current($keys);
        }

        try {
            $decoded = JWT::decode(
                $token,
                $keys
            );

            return (array) $decoded;
        } catch (Exception $exception) {
            $this->log(LogLevel::WARNING, $exception->getMessage(), [$token]);

            throw $exception;
        }
    }

    /**
     * @return array<int|string,Key>
     */
    private function createKeysFromAlgorithms(): array
    {
        $keyObjects = [];

        foreach ($this->options->algorithm as $kid => $algorithm) {
            $keyId = is_numeric($kid) ? $algorithm : $kid;

            $secret = $this->options->secret[$kid];

            $keyObjects[$keyId] = new Key($secret, $algorithm);
        }

        return $keyObjects;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param  mixed[]  $context
     */
    private function log(string $level, string $message, array $context = []): void
    {
        if ($this->logger instanceof LoggerInterface) {
            $this->logger->log($level, $message, $context);
        }
    }

    /**
     * Set the rules.
     *
     * @param  RuleInterface[]  $rules
     */
    private function rules(array $rules): void
    {
        foreach ($rules as $callable) {
            $this->rules->push($callable);
        }
    }
}

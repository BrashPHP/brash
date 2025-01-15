<?php

namespace Tests\Traits\App;

use Exception;
use Nyholm\Psr7\Uri;
use Psr\Http\Message\ServerRequestInterface;
use Tests\Builders\Request\RequestBuilder;

class BadRequestConfig extends Exception {}

trait RequestManagerTrait
{
    public const string FORMAT = 'application/json';

    public function createRequest(
        string $method,
        string $path,
        array $headers = [
            'HTTP_ACCEPT' => self::FORMAT,
            'Content-Type' => self::FORMAT,
        ],
        array $serverParams = [],
        array $cookies = []
    ): ServerRequestInterface {
        $psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory;
        $uri = $psr17Factory->createUri($path)->withPort(80);
        $handle = 'php://temp';
        $stream = $psr17Factory->createStream($handle);
        $request = $psr17Factory->createServerRequest($method, $uri, $serverParams);

        foreach ($headers as $name => $value) {
            $request = $request->withAddedHeader($name, $value);
        }

        $request = $request->withCookieParams($cookies);

        return $request->withBody($stream);
    }

    protected function constructPostRequest(
        array|object $data,
        string $method,
        string $path,
        ?array $headers = null,
        ?array $serverParams = null,
        ?array $cookies = null
    ): ServerRequestInterface {
        if (! ($method && $path)) {
            throw new BadRequestConfig('Unable to create request');
        }

        $requestBuilder = new RequestBuilder($method, $path);
        if ($headers) {
            $requestBuilder->withHeaders($headers);
        }

        if ($serverParams) {
            $requestBuilder->withServerParam($serverParams);
        }

        if ($cookies) {
            $requestBuilder->withCookies($cookies);
        }

        $request = $requestBuilder->build();
        $this->setRequestParsedBody($request, $data);

        return $request;
    }

    protected function setRequestParsedBody(ServerRequestInterface $request, array|object $data): ServerRequestInterface
    {
        $encodedData = json_encode($data);
        $request->getBody()->write($encodedData);
        $request->getBody()->rewind();
        $requestParsedData = json_decode($encodedData, true);

        return $request->withParsedBody($requestParsedData);
    }

    /**
     * Create a JSON request.
     *
     * @param  string  $method  The HTTP method
     * @param  string|\Psr\Http\Message\UriInterface  $uri  The URI
     * @param  null|array  $data  The json data
     */
    protected function createJsonRequest(
        string $method,
        $uri,
        array|object|null $data = null
    ): ServerRequestInterface {
        $request = $this->createRequest($method, $uri);

        if ($data !== null) {
            $request = $this->setRequestParsedBody($request, $data);
        }

        return $request->withHeader('Content-Type', self::FORMAT);
    }
}

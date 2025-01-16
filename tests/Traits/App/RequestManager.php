<?php

namespace Tests\Traits\App;

use Psr\Http\Message\ServerRequestInterface;

class RequestManager
{
    private const string CONTENT_TYPE = 'application/json';

    public function createRequest(
        string $method,
        string $path,
        array $headers = [
            'HTTP_ACCEPT' => self::CONTENT_TYPE,
            'Content-Type' => self::CONTENT_TYPE,
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

    public function constructPostRequest(
        array|object $data,
        string $method,
        string $path,
        ?array $headers = null,
        ?array $serverParams = null,
        ?array $cookies = null
    ): ServerRequestInterface {
        $psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory;
        $uri = $psr17Factory->createUri($path)->withPort(80);

        $request = $psr17Factory->createServerRequest($method, $uri, $serverParams);

        foreach ($headers as $name => $value) {
            $request = $request->withAddedHeader($name, $value);
        }

        $request = $request->withCookieParams($cookies);

        return $this->setRequestParsedBody($request, $data);
    }

    public function setRequestParsedBody(ServerRequestInterface $request, array|object $data): ServerRequestInterface
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

        return $request->withHeader('Content-Type', self::CONTENT_TYPE);
    }
}

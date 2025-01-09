<?php

namespace Brash\Framework\Http\Factories;

use Psr\Http\Message\ServerRequestInterface;

class RequestFactory
{
    // Create Request object from globals
    public function createRequest(): ServerRequestInterface
    {
        $psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory;

        $creator = new \Nyholm\Psr7Server\ServerRequestCreator(
            serverRequestFactory: $psr17Factory,
            // ServerRequestFactory
            uriFactory: $psr17Factory,
            // UriFactory
            uploadedFileFactory: $psr17Factory,
            // UploadedFileFactory
            streamFactory: $psr17Factory // StreamFactory
        );

        return $creator->fromGlobals();
    }
}

<?php

namespace Core\Http\Factories;

use Psr\Http\Message\ServerRequestInterface;

class RequestFactory
{
    // Create Request object from globals
    public function createRequest(): ServerRequestInterface
    {
        $psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();

        $creator = new \Nyholm\Psr7Server\ServerRequestCreator(
            $psr17Factory,
            // ServerRequestFactory
            $psr17Factory,
            // UriFactory
            $psr17Factory,
            // UploadedFileFactory
            $psr17Factory // StreamFactory
        );

        return $creator->fromGlobals();
    }
}

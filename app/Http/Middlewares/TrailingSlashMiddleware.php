<?php

declare(strict_types=1);

namespace Core\Http\Middlewares;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TrailingSlashMiddleware implements MiddlewareInterface
{
    private ResponseFactoryInterface $responseFactory;

    /**
     * Configure whether add or remove the slash.
     */
    public function __construct(private bool $trailingSlash = false)
    {
        $this->responseFactory = new \Nyholm\Psr7\Factory\Psr17Factory();
    }

    /**
     * Process a request and return a response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uri = $request->getUri();
        $path = $this->normalize($uri->getPath());

        if ($uri->getPath() !== "/" && $this->responseFactory && ($uri->getPath() !== $path)) {
            return $this->responseFactory->createResponse(301)
                ->withHeader('Location', (string) $uri->withPath($path));
        }

        return $handler->handle($request->withUri($uri->withPath($path)));
    }

    private function normalize(string $path): string
    {
        if ($path === '') {
            return '/';
        }

        if (
            $this->trailingSlash
            && !\str_ends_with($path, '/')
            && !pathinfo($path, PATHINFO_EXTENSION)
        ) {
            return "{$path}/";

        }
        return rtrim($path, '/');
    }
}

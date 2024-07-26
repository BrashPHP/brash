<?php
namespace Core\Http\Adapters\SlimFramework;

use Core\Http\Interfaces\ApplicationInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * App is a proxy to receive a request and emit a response, wrapping all the operations within the execution flow.
 * Currently, based on Slim's App.
 */
final class SlimAppAdapter implements ApplicationInterface
{

    public function __construct(private \Slim\App $slimApp)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->slimApp->handle($request);
    }
}

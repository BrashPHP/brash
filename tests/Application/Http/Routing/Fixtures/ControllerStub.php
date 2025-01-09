<?php

namespace Tests\Application\Http\Routing\Fixtures;

use Core\Http\Interfaces\ActionInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\Promise;

class ControllerStub implements ActionInterface
{
    public function action(ServerRequestInterface $request): ResponseInterface|Promise
    {
        return new Response;
    }
}

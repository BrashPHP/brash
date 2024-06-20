<?php


namespace Core\Http\Interfaces;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use React\Promise\Promise;

interface ActionInterface
{
    /**
     * @return Response|Promise<Response>
     */
    public function action(Request $request): Response|Promise;
}

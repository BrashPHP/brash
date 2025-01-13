<?php

declare(strict_types=1);

namespace Brash\Framework\Http\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;

class FiberMiddleware
{

    public function __invoke(ServerRequestInterface $request, callable $next): PromiseInterface
    {
        $deferred = null;
        $fiber = new \Fiber(
            function () use ($request, $next, &$deferred) {
                $response = $next($request);
                assert($response instanceof ResponseInterface || $response instanceof PromiseInterface || $response instanceof \Generator);

                // if the next request handler returns immediately, the fiber can terminate immediately without using a Deferred
                // if the next request handler suspends the fiber, we only reach this point after resuming the fiber, so the code below will have assigned a Deferred
                /**
                 * @var ?Deferred<ResponseInterface> $deferred
                 */
                if ($deferred instanceof Deferred) {
                    assert($response instanceof ResponseInterface);
                    $deferred->resolve($response);
                }

                return $response;
            }
        );

        /**
         * @throws void because the next handler will always be an `ErrorHandler`
         */
        $fiber->start();
        if ($fiber->isTerminated()) {
            /**
             * @throws void because fiber is known to have terminated successfully
             */
            /**
             * @var ResponseInterface|PromiseInterface<ResponseInterface>|\Generator
             */
            return $fiber->getReturn();
        }

        $deferred = new Deferred;

        return $deferred->promise();
    }
}

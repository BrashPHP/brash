<?php

declare(strict_types=1);

namespace Brash\Framework\Http\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;

class FiberMiddleware
{
    /**
     * @return ResponseInterface|PromiseInterface<ResponseInterface>|\Generator
     *                                                                          Returns a `ResponseInterface` from the next request handler in the
     *                                                                          chain. If the next request handler returns immediately, this method
     *                                                                          will return immediately. If the next request handler suspends the
     *                                                                          fiber (see `await()`), this method will return a `PromiseInterface`
     *                                                                          that is fulfilled with a `ResponseInterface` when the fiber is
     *                                                                          terminated successfully. If the next request handler returns a
     *                                                                          promise, this method will return a promise that follows its
     *                                                                          resolution. If the next request handler returns a Generator-based
     *                                                                          coroutine, this method returns a `Generator`. This method never
     *                                                                          throws or resolves a rejected promise. If the handler fails, it will
     *                                                                          be turned into a valid error response before returning.
     *
     * @throws void
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
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
                if ($deferred instanceof \React\Promise\Deferred) {
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

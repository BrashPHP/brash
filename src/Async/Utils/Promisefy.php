<?php

namespace Brash\Framework\Async\Utils;

use React\Promise\Promise;

class Promisefy
{
    public static function promisify($callbackBasedFunction): callable
    {
        return fn (...$args): \React\Promise\Promise => new Promise(function ($resolve, $reject) use ($args, $callbackBasedFunction): void {
            $callbackBasedFunction(...$args, function ($error, $result) use ($resolve, $reject): void {
                if ($error) {
                    $reject($error);
                } else {
                    $resolve($result);
                }
            });
        });
    }
}

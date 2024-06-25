<?php
namespace Core\Async\Utils;

use React\Promise\Promise;

class Promisefy
{
    public static function promisify($callbackBasedFunction): callable
    {
        return function (...$args) use ($callbackBasedFunction) {
            return new Promise(function ($resolve, $reject) use ($args, $callbackBasedFunction) {
                $callbackBasedFunction(...$args, function ($error, $result) use ($resolve, $reject) {
                    if ($error) {
                        $reject($error);
                    } else {
                        $resolve($result);
                    }
                });
            });
        };
    }
}

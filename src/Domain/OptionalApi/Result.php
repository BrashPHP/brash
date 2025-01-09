<?php

/**
 * Result
 *
 * Documentation and API borrowed from Rust: https://doc.rust-lang.org/std/result/enum.Result.html
 *
 * @author Oskar Thornblad
 */

declare(strict_types=1);

namespace App\Domain\OptionalApi;

use App\Domain\OptionalApi\Result\ResultException;
use Exception;
use PhpOption\Option;

/**
 * Describes a Result
 *
 * @template T
 * The Ok value
 * @template E
 * The Err value
 */
abstract class Result
{
    /**
     * Returns true if the result is Ok.
     */
    abstract public function isOk(): bool;

    /**
     * Returns true if the result is Err.
     */
    abstract public function isErr(): bool;

    /**
     * Converts from Result<T, E> to Option<T>, and discarding the error, if any
     *
     * @psalm-return Option<T>
     */
    abstract public function ok(): Option;

    /**
     * Converts from Result<T, E> to Option<E>, and discarding the value, if any
     *
     * @psalm-return Option<E>
     */
    abstract public function err(): Option;

    /**
     * Maps a Result by applying a function to a contained Ok value, leaving an Err value untouched.
     *
     * @template U
     *
     * @psalm-param  callable(T=,mixed...):U $mapper
     *
     * @psalm-return Result<U,E>
     */
    abstract public function map(callable $mapper): Result;

    /**
     * Maps a Result by applying a function to a contained Err value, leaving an Ok value untouched.
     *
     * @template F
     *
     * @psalm-param  callable(E=,mixed...):F $mapper
     *
     * @psalm-return Result<T,F>
     */
    abstract public function mapErr(callable $mapper): Result;

    /**
     * Returns an iterator over the possibly contained value.
     * The iterator yields one value if the result is Ok, otherwise none.
     *
     * @psalm-return array<int, T>
     */
    abstract public function iter(): array;

    /**
     * Returns res if the result is Ok, otherwise returns the Err value of self.
     *
     * @template U
     *
     * @psalm-param  Result<U,E> $res
     *
     * @psalm-return Result<U,E>
     */
    abstract public function and(Result $res): Result;

    /**
     * Calls op if the result is Ok, otherwise returns the Err value of self.
     *
     * @template U
     *
     * @psalm-param  callable(T=,mixed...):Result<U,E> $op
     *
     * @psalm-return Result<U,E>
     */
    abstract public function andThen(callable $op): Result;

    /**
     * Returns res if the result is Err, otherwise returns the Ok value of self.
     *
     * @template F
     *
     * @psalm-param  Result<T,F> $res
     *
     * @psalm-return Result<T,F>
     */
    abstract public function or(Result $res): Result;

    /**
     * Calls op if the result is Err, otherwise returns the Ok value of self.
     *
     * @template F
     *
     * @psalm-param  callable(E=,mixed...):Result<T,F> $op
     *
     * @psalm-return Result<T,F>
     */
    abstract public function orElse(callable $op): Result;

    /**
     * Unwraps a result, yielding the content of an Ok. Else, it returns optb.
     *
     * @param  mixed  $optb
     *
     * @psalm-param  T $optb
     *
     * @return mixed
     *
     * @psalm-return T
     */
    abstract public function unwrapOr($optb);

    /**
     * Unwraps a result, yielding the content of an Ok. If the value is an Err then it calls op with its value.
     *
     * @psalm-param  callable(E=,mixed...):T $op
     *
     * @return mixed
     *
     * @psalm-return T
     */
    abstract public function unwrapOrElse(callable $op);

    /**
     * Unwraps a result, yielding the content of an Ok.
     *
     * @return mixed
     *
     * @psalm-return T
     *
     * @throws Exception if the value is an Err.
     */
    abstract public function unwrap();

    /**
     * Unwraps a result, yielding the content of an Ok.
     *
     * @template X as Exception
     *
     * @psalm-param  X&Exception $msg
     *
     * @return mixed
     *
     * @psalm-return T
     *
     * @throws Exception the message if the value is an Err.
     */
    abstract public function expect(Exception $msg);

    /**
     * Unwraps a result, yielding the content of an Err.
     *
     * @return mixed
     *
     * @psalm-return E
     *
     * @throws ResultException if the value is an Ok.
     */
    abstract public function unwrapErr();

    /**
     * Applies values inside the given Results to the function in this Result.
     *
     * @param  Result  ...$inArgs  Results to apply the function to.
     *
     * @psalm-return Result<mixed,E>
     */
    abstract public function apply(Result ...$inArgs): Result;

    /**
     * The attached pass-through args will be unpacked into extra args into chained callables
     *
     * @param  mixed  ...$args
     *
     * @psalm-return Result<T,E>
     */
    abstract public function with(...$args): Result;
}

<?php

/**
 * Err
 *
 * Documentation and API borrowed from Rust: https://doc.rust-lang.org/std/result/enum.Result.html
 *
 * @author Oskar Thornblad
 */

declare(strict_types=1);

namespace App\Domain\OptionalApi\Result;

use App\Domain\OptionalApi\Result;
use Exception;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;

/**
 * Err
 *
 * @template T
 * The Ok value
 * @template E
 * The Err value
 *
 * @extends Result<T, E>
 */
class Err extends Result
{
    /**
     * @var mixed
     *
     * @psalm-var E
     */
    private $err;

    /**
     * @var array
     *
     * @psalm-var list<mixed>
     */
    private $pass;

    /**
     * Err constructor.
     *
     * @param  mixed  $err
     *
     * @psalm-param E $err
     *
     * @param  mixed  ...$pass
     */
    public function __construct($err, ...$pass)
    {
        $this->err = $err;
        $this->pass = $pass;
    }

    /**
     * Returns true if the result is Ok.
     */
    public function isOk(): bool
    {
        return false;
    }

    /**
     * Returns true if the result is Err.
     */
    public function isErr(): bool
    {
        return true;
    }

    /**
     * Maps a Result by applying a function to a contained Ok value, leaving an Err value untouched.
     *
     * @template U
     *
     * @psalm-param  callable(T=,mixed...):U $mapper
     *
     * @psalm-return Result<U,E>
     */
    public function map(callable $mapper): Result
    {
        return new self($this->err, ...$this->pass);
    }

    /**
     * Maps a Result by applying a function to a contained Err value, leaving an Ok value untouched.
     *
     * @template F
     *
     * @psalm-param  callable(E=,mixed...):F $mapper
     *
     * @psalm-return Result<T,F>
     */
    public function mapErr(callable $mapper): Result
    {
        return new self($mapper($this->err, ...$this->pass));
    }

    /**
     * Returns an iterator over the possibly contained value.
     * The iterator yields one value if the result is Ok, otherwise none.
     *
     * @psalm-return array<int, T>
     */
    public function iter(): array
    {
        return [];
    }

    /**
     * Returns res if the result is Ok, otherwise returns the Err value of self.
     *
     * @template U
     *
     * @psalm-param  Result<U,E> $res
     *
     * @psalm-return Result<U,E>
     */
    public function and(Result $res): Result
    {
        return new self($this->err, ...$this->pass);
    }

    /**
     * Calls op if the result is Ok, otherwise returns the Err value of self.
     *
     * @template U
     *
     * @psalm-param  callable(T=,mixed...):Result<U,E> $op
     *
     * @psalm-return Result<U,E>
     */
    public function andThen(callable $op): Result
    {
        return new self($this->err, ...$this->pass);
    }

    /**
     * Returns res if the result is Err, otherwise returns the Ok value of self.
     *
     * @template F
     *
     * @psalm-param  Result<T,F> $res
     *
     * @psalm-return Result<T,F>
     */
    public function or(Result $res): Result
    {
        return $res;
    }

    /**
     * Calls op if the result is Err, otherwise returns the Ok value of self.
     *
     * @template F
     *
     * @psalm-param  callable(E=,mixed...):Result<T,F> $op
     *
     * @psalm-return Result<T,F>
     *
     * @psalm-assert !callable(T=):Result $op
     */
    public function orElse(callable $op): Result
    {
        return $op($this->err, ...$this->pass);
    }

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
    public function unwrapOr($optb)
    {
        return $optb;
    }

    /**
     * Unwraps a result, yielding the content of an Ok. If the value is an Err then it calls op with its value.
     *
     * @psalm-param  callable(E=,mixed...):T $op
     *
     * @return mixed
     *
     * @psalm-return T
     */
    public function unwrapOrElse(callable $op)
    {
        return $op($this->err, ...$this->pass);
    }

    /**
     * Unwraps a result, yielding the content of an Ok.
     *
     * @return void
     *
     * @psalm-return never-return
     *
     * @throws Exception if the value is an Err.
     */
    public function unwrap()
    {
        if ($this->err instanceof Exception) {
            throw $this->err;
        }

        throw new ResultException('Unwrapped an Err');
    }

    /**
     * Unwraps a result, yielding the content of an Ok.
     *
     * @template X as Exception
     *
     * @psalm-param  X&Exception $msg
     *
     * @return void
     *
     * @psalm-return never-return
     *
     * @throws Exception the message if the value is an Err.
     */
    public function expect(Exception $msg)
    {
        throw $msg;
    }

    /**
     * Unwraps a result, yielding the content of an Err.
     *
     * @return mixed
     *
     * @psalm-return E
     */
    public function unwrapErr()
    {
        return $this->err;
    }

    /**
     * Applies values inside the given Results to the function in this Result.
     *
     * @param  Result  ...$inArgs  Results to apply the function to.
     *
     * @psalm-return Result<mixed,E>
     */
    public function apply(Result ...$inArgs): Result
    {
        return $this;
    }

    /**
     * Converts from Result<T, E> to Option<T>, and discarding the error, if any
     *
     * @psalm-return Option<T>
     */
    public function ok(): Option
    {
        return new None;
    }

    /**
     * Converts from Result<T, E> to Option<E>, and discarding the value, if any
     *
     * @psalm-return Option<E>
     */
    public function err(): Option
    {
        return new Some($this->err);
    }

    /**
     * The attached pass-through args will be unpacked into extra args into chained callables
     *
     * @param  mixed  ...$args
     *
     * @psalm-return Result<T,E>
     */
    public function with(...$args): Result
    {
        $this->pass = $args;

        return $this;
    }
}

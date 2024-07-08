<?php

namespace App\Infrastructure\Persistence\Abstraction;

use App\Domain\Contracts\ModelInterface;
use App\Domain\Repositories\PersistenceOperations\CrudOperationsInterface;
use App\Domain\Repositories\PersistenceOperations\Responses\ResultSetInterface;
use App\Infrastructure\Persistence\Contracts\RepositoryInterface;
use App\Infrastructure\Persistence\PersistenceUtils\ItemsRetriever;

/**
 * @template T of object
 */
abstract class AbstractRepository implements CrudOperationsInterface
{

    public function __construct(protected ItemsRetriever $itemsRetriever)
    {
    }

    /**
     * @return class-string<T>
     */
    abstract public function entity(): string;

    /**
     * @return RepositoryInterface<T>
     */
    abstract public function repository(): RepositoryInterface;

    public function findAll(bool $paginate = false, int $page = 1, int $limit = 20): ResultSetInterface
    {
        return $this->itemsRetriever->findAll($this->entity(), $paginate, $page, $limit);
    }

    /**
     * @return ?T
     */
    #[\ReturnTypeWillChange]
    public function findByKey(string $key, mixed $value): ?object
    {
        return $this->repository()->findOne([$key => $value]);
    }

    /**
     * @return T[]
     */
    #[\ReturnTypeWillChange]
    public function findItemsByKey(string $key, mixed $value): array
    {
        return $this->repository()->findBy([$key => $value]);
    }

    /**
     * @return ?T
     */
    #[\ReturnTypeWillChange]
    public function findByID(int $id): ?object
    {
        return $this->repository()->findByPK($id);
    }

    /**
     * @return T[]
     */
    #[\ReturnTypeWillChange]
    public function findWithConditions(array $conditions): array
    {
        return $this->repository()->findBy($conditions);
    }

    /**
     * @param T|int $subject
     *
     * @return ?T
     */
    #[\ReturnTypeWillChange]
    abstract public function delete(ModelInterface|int $subject): ?object;

    /**
     * @param T
     */
    abstract public function insert(ModelInterface $model): void;
}

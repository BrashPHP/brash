<?php

namespace App\Domain\Repositories\PersistenceOperations;

interface UpdateOperationInterface
{
    #[\ReturnTypeWillChange]
    public function update(int $id, array $values): ?object;
}

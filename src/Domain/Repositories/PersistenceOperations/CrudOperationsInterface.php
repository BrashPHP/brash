<?php

namespace App\Domain\Repositories\PersistenceOperations;

interface CrudOperationsInterface extends DeleteOperationInterface, PersistOperationInterface, ReadOperationInterface, UpdateOperationInterface {}

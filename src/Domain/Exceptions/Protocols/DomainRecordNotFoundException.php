<?php

declare(strict_types=1);

namespace App\Domain\Exceptions\Protocols;
use Core\Http\Errors\HttpExceptionAdapter;

abstract class DomainRecordNotFoundException extends HttpExceptionAdapter
{
}

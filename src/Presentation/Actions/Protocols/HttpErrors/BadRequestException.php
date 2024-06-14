<?php

namespace App\Presentation\Actions\Protocols\HttpErrors;

use Slim\Exception\HttpBadRequestException;
use Throwable;

class BadRequestException extends HttpBadRequestException
{
    protected $code = 400;
    
    protected ?Throwable $previous = null;
    
    protected string $title = '400 Bad Request';
    
    protected string $description = 'The request was well-formed but unable to be followed due to semantic errors.';
}

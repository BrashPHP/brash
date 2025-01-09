<?php

namespace Core\Http\Middlewares\BodyParsing\Exceptions;

final class BadBodyReturn extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Request body media type parser return value must be an array, an object, or null');
    }
}

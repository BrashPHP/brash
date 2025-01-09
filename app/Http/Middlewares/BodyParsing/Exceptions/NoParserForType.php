<?php

namespace Core\Http\Middlewares\BodyParsing\Exceptions;

final class NoParserForType extends \RuntimeException
{
    public function __construct(string $mediaType)
    {
        parent::__construct('No parser for type '.$mediaType);
    }
}

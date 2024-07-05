<?php
namespace Core\Http\Middlewares\BodyParsing\Parsers;

final class JsonBodyParser
{
    public function parse(string $input): ?array
    {
        if (\json_validate($input)) {
            $result = json_decode($input, true);
            if (\is_array($result)) {
                return $result;
            }
        }

        return null;
    }
}

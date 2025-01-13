<?php

namespace Brash\Framework\Cli\Services;

class FindProjectRootService
{
    public function get(string $input): ?string
    {
        if (is_dir($input)) {
            return $input;
        }

        $projectRoot = null;
        for ($i = 1; $i <= 7; $i++) {
            $vendorPath = \dirname(__DIR__, $i) . \DIRECTORY_SEPARATOR . "vendor" . \DIRECTORY_SEPARATOR . "autoload.php";
            if (is_file($vendorPath)) {
                $projectRoot = dirname($vendorPath, 2);
                break;
            }
        }
        $input = ltrim($input, "/");
        $path = join(DIRECTORY_SEPARATOR, [$projectRoot, $input]);
        $invalidPath = !(is_dir($path) || is_file($path));

        if ($projectRoot === null || $invalidPath) {
            return null;
        }

        return $path;
    }
}

<?php

namespace Core\functions;

if (!function_exists('getBytes')) {
    /**
     * Summary of Core\functions\getBytes
     *
     * @param string $string
     * @param string $charset
     * @return array
     */
    function getBytes(string $string, string $charset = 'UTF-8'): array
    {
        return array_values(
            unpack('C*', mb_convert_encoding($string, 'UTF-8', $charset))
        );
    }
}

<?php

declare(strict_types=1);

if (!\function_exists('str_starts_with')) {
    /**
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    function str_starts_with(string $haystack, string $needle): bool
    {
        return 0 === \substr_compare($haystack, $needle, 0, \strlen($needle));
    }
}

if (!\function_exists('str_ends_with')) {
    /**
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    function str_ends_with(string $haystack, string $needle): bool
    {
        return 0 === \substr_compare($haystack, $needle, -\strlen($needle));
    }
}
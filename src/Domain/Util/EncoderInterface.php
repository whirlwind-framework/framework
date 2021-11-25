<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Util;

interface EncoderInterface
{
    public function encode(string $str): string;

    public function validateHash(string $str, string $hash): bool;
}

<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Factory;

final class UidFactory implements UidFactoryInterface
{
    public function create(string $prefix = ''): string
    {
        return \uniqid($prefix);
    }
}

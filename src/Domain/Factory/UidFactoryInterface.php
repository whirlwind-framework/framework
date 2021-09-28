<?php declare(strict_types=1);

namespace Whirlwind\Domain\Factory;

interface UidFactoryInterface
{
    public function create(string $prefix = ''): string;
}

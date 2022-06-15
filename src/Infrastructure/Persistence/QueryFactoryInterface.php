<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Persistence;

interface QueryFactoryInterface
{
    public function create(ConnectionInterface $connection): QueryInterface;
}

<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Repository;

interface ResultFactoryInterface
{
    public function create(array $data): ResultInterface;
}

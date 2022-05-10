<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Repository;

use Whirlwind\Domain\Repository\ResultFactoryInterface;
use Whirlwind\Domain\Repository\ResultInterface;

class ResultFactory implements ResultFactoryInterface
{
    public function create(array $data): ResultInterface
    {
        return new Result($data);
    }
}

<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Persistence;

interface ConditionBuilderInterface
{
    public function build(array $params): array;
}

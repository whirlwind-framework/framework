<?php declare(strict_types=1);

namespace Whirlwind\Infrastructure\Hydrator\Strategy;

interface StrategyInterface
{
    public function extract($value, ?object $object = null);

    public function hydrate($value, ?array $data = null, $oldValue = null);
}

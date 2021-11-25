<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Hydrator\Strategy;

use Whirlwind\Infrastructure\Hydrator\Hydrator;

class ObjectStrategy implements StrategyInterface
{
    protected $hydrator;

    protected $entityName;

    public function __construct(Hydrator $hydrator, string $entityName)
    {
        $this->hydrator = $hydrator;
        $this->entityName = $entityName;
    }

    public function hydrate($value, ?array $data = null, $oldValue = null)
    {
        $data = \array_merge((array)$oldValue, (array)$value);
        return $this->hydrator->hydrate($this->entityName, $data);
    }

    public function extract($value, ?object $object = null)
    {
        if (!\is_object($value)) {
            return [];
        }
        return $this->hydrator->extract($value);
    }
}

<?php declare(strict_types=1);

namespace Whirlwind\Infrastructure\Hydrator\Strategy;

use Whirlwind\Domain\Collection\CollectionInterface;
use Whirlwind\Infrastructure\Hydrator\Hydrator;

class CollectionStrategy implements StrategyInterface
{
    protected $hydrator;

    protected $entityName;

    protected $collectionName;

    public function __construct(Hydrator $hydrator, string $entityName, string $collectionName)
    {
        $this->hydrator = $hydrator;
        $this->entityName = $entityName;
        $this->collectionName = $collectionName;
    }

    public function hydrate($value, ?array $data = null, $oldValue = null)
    {
        $value = (array)$value;
        $items = \array_map(function ($data) {
            return $this->hydrator->hydrate($this->entityName, $data);
        }, $value);
        return new $this->collectionName($items);
    }

    public function extract($value, ?object $object = null)
    {
        if (!($value instanceof CollectionInterface)) {
            return [];
        }
        $result = [];
        foreach ($value as $item) {
            $result[] = $this->hydrator->extract($item);
        }
        return $result;
    }
}

<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Hydrator\Strategy;

use Whirlwind\Domain\Collection\CollectionInterface;
use Whirlwind\Domain\Enum;
use Whirlwind\Infrastructure\Hydrator\Strategy\StrategyInterface;

class EnumCollectionStrategy implements StrategyInterface
{
    /**
     * @var string
     */
    protected $enumClass;
    /**
     * @var string
     */
    protected $collectionName;

    /**
     * EnumCollectionStrategy constructor.
     * @param string $enumClass
     */
    public function __construct(string $enumClass, string $collectionName)
    {
        if (!\is_subclass_of($enumClass, Enum::class)) {
            throw new \InvalidArgumentException('Class is not subclass of ' . Enum::class);
        }
        $this->enumClass = $enumClass;
        $this->collectionName = $collectionName;
    }

    /**
     * @param $value
     * @param object|null $object
     * @return array
     */
    public function extract($value, ?object $object = null): array
    {
        if (!($value instanceof CollectionInterface)) {
            return [];
        }
        $result = [];
        /** @var Enum $item */
        foreach ($value as $item) {
            $result[] = $item->getValue();
        }
        return $result;
    }

    /**
     * @param $value
     * @param array|null $data
     * @param null $oldValue
     * @return CollectionInterface
     */
    public function hydrate($value, ?array $data = null, $oldValue = null): CollectionInterface
    {
        $value = (array)$value;
        $items = \array_map(function ($data) {
            return new $this->enumClass($data);
        }, $value);
        return new $this->collectionName($items);
    }
}

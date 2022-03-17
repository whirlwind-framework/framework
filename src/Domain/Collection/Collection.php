<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Collection;

class Collection implements CollectionInterface
{
    protected $items;

    protected $entityClass;

    public function __construct(string $entityClass, array $items = [])
    {
        $this->entityClass = $entityClass;
        $this->items = [];
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    public function add($item): void
    {
        if (!($item instanceof $this->entityClass)) {
            throw new \InvalidArgumentException("Invalid object provided. Expected: " . $this->entityClass);
        }
        $this->items[] = $item;
    }

    public function offsetSet($key, $item): void
    {
        if (!($item instanceof $this->entityClass)) {
            throw new \InvalidArgumentException("Invalid object provided. Expected: " . $this->entityClass);
        }
        if ($key === null) {
            $this->items[] = $item;
        } else {
            $this->items[$key] = $item;
        }
    }

    public function offsetUnset($key): void
    {
        if (\array_key_exists($key, $this->items)) {
            unset($this->items[$key]);
        }
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($key)
    {
        if (\array_key_exists($key, $this->items)) {
            return $this->items[$key];
        }
        return null;
    }

    public function offsetExists($key): bool
    {
        return \array_key_exists($key, $this->items);
    }

    public function clear(): void
    {
        $this->items = [];
    }

    public function rewind(): void
    {
        \reset($this->items);
    }

    #[\ReturnTypeWillChange]
    public function current()
    {
        return \current($this->items);
    }

    public function next(): void
    {
        \next($this->items);
    }

    #[\ReturnTypeWillChange]
    public function key()
    {
        return \key($this->items);
    }

    public function valid(): bool
    {
        return (bool)$this->current();
    }

    public function count(): int
    {
        return \count($this->items);
    }

    /**
     * @return mixed|null
     */
    public function first()
    {
        $first = \reset($this->items);

        return $first === false ? null : $first;
    }

    /**
     * @return mixed|null
     */
    public function last()
    {
        $last = \end($this->items);

        return $last === false ? null : $last;
    }

    /**
     * @param callable $callback
     * @param int $mode
     * @return $this
     */
    public function filter(callable $callback, int $mode = 0): self
    {
        $copy = clone $this;
        $copy->items = \array_filter($this->items, $callback, $mode);

        return $copy;
    }

    /**
     * @param callable $callback
     * @param $initial
     * @return mixed|null
     */
    public function reduce(callable $callback, $initial = null)
    {
        return \array_reduce($this->items, $callback, $initial);
    }
}

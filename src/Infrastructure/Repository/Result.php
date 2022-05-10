<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Repository;

use Whirlwind\Domain\Repository\ResultInterface;

class Result implements ResultInterface
{
    protected array $items;

    /**
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function current()
    {
        return \current($this->items);
    }

    public function next()
    {
        return \next($this->items);
    }

    public function key()
    {
        return \key($this->items);
    }

    public function valid(): bool
    {
        return false !== $this->current();
    }

    public function rewind()
    {
        \reset($this->items);
    }

    public function offsetExists($offset)
    {
        return \array_key_exists($offset, $this->items);
    }

    public function offsetGet($offset)
    {
        return $this->items[$offset] ?? null;
    }

    public function offsetSet($offset, $value)
    {
        return $this->items[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }
}

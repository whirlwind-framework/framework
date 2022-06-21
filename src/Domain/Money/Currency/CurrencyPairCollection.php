<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Money\Currency;

use Whirlwind\Domain\Collection\Collection;
use Whirlwind\Domain\Money\CurrencyInterface;

/**
 * @property CurrencyPair[] $items
 * @method CurrencyPair|false current()
 * @method CurrencyPair|false reduce(callable $callback, $initial = null)
 */
class CurrencyPairCollection extends Collection
{
    /**
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        parent::__construct(CurrencyPair::class, $items);
    }

    /**
     * @param CurrencyInterface $base
     * @param CurrencyInterface $target
     * @return CurrencyPair|null
     */
    public function findByBaseAndTarget(CurrencyInterface $base, CurrencyInterface $target): ?CurrencyPair
    {
        foreach ($this->items as $item) {
            if ($item->getBase()->equals($base) && $item->getTarget()->equals($target)) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param CurrencyPairCollection $other
     * @return $this
     */
    public function merge(CurrencyPairCollection $other): self
    {
        foreach ($other as $item) {
            if (!$this->findByBaseAndTarget($item->getBase(), $item->getTarget())) {
                $this->add($item);
            }
        }

        return $this;
    }

    /**
     * @param CurrencyPair $pair
     * @return void
     */
    public function addUniquePair(CurrencyPair $pair): void
    {
        $this->items = $this->filter(static function (CurrencyPair $item) use ($pair) {
            return !$item->getBase()->equals($pair->getBase()) || !$item->getTarget()->equals($pair->getTarget());
        })->items;
        $this->add($pair);
    }
}

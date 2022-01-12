<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Money;

class MoneyService
{
    /**
     * @var MoneyComparatorInterface
     */
    protected $comparator;

    /**
     * @var MoneyCalculatorInterface
     */
    protected $calculator;

    /**
     * @param MoneyComparatorInterface $comparator
     * @param MoneyCalculatorInterface $calculator
     */
    public function __construct(MoneyComparatorInterface $comparator, MoneyCalculatorInterface $calculator)
    {
        $this->comparator = $comparator;
        $this->calculator = $calculator;
    }

    /**
     * @param MoneyInterface $money
     * @param MoneyInterface ...$addends
     * @return MoneyInterface
     */
    public function add(MoneyInterface $money, MoneyInterface ...$addends): MoneyInterface
    {
        return $this->calculator->add($money, ...$addends);
    }

    /**
     * @param MoneyInterface $minuend
     * @param MoneyInterface ...$subtrahends
     * @return MoneyInterface
     */
    public function subtract(MoneyInterface $minuend, MoneyInterface ...$subtrahends): MoneyInterface
    {
        return $this->calculator->subtract($minuend, ...$subtrahends);
    }

    /**
     * @param MoneyInterface $money
     * @param int|float|string $multiplier
     * @param int $mode
     * @return MoneyInterface
     */
    public function multiply(MoneyInterface $money, $multiplier, int $mode = PHP_ROUND_HALF_UP): MoneyInterface
    {
        return $this->calculator->multiply($money, $multiplier, $mode);
    }

    /**
     * @param MoneyInterface $money
     * @param int|float|string $divisor
     * @param int $mode
     * @return MoneyInterface
     */
    public function divide(MoneyInterface $money, $divisor, int $mode = PHP_ROUND_HALF_UP): MoneyInterface
    {
        return $this->calculator->divide($money, $divisor, $mode);
    }

    /**
     * @param MoneyInterface $money
     * @param MoneyInterface $other
     * @return int
     */
    public function compare(MoneyInterface $money, MoneyInterface $other): int
    {
        return $this->comparator->compare($money, $other);
    }

    /**
     * @param MoneyInterface $money
     * @param MoneyInterface $other
     * @return bool
     */
    public function isSameCurrency(MoneyInterface $money, MoneyInterface $other): bool
    {
        return $this->comparator->isSameCurrency($money, $other);
    }

    /**
     * @param MoneyInterface $money
     * @param MoneyInterface $other
     * @return bool
     */
    public function equals(MoneyInterface $money, MoneyInterface $other): bool
    {
        return $this->comparator->equals($money, $other);
    }

    /**
     * @param MoneyInterface $money
     * @param MoneyInterface $other
     * @return bool
     */
    public function greaterThan(MoneyInterface $money, MoneyInterface $other): bool
    {
        return $this->comparator->greaterThan($money, $other);
    }

    /**
     * @param MoneyInterface $money
     * @param MoneyInterface $other
     * @return bool
     */
    public function greaterThanOrEqual(MoneyInterface $money, MoneyInterface $other): bool
    {
        return $this->comparator->greaterThanOrEqual($money, $other);
    }

    /**
     * @param MoneyInterface $money
     * @param MoneyInterface $other
     * @return bool
     */
    public function lessThan(MoneyInterface $money, MoneyInterface $other): bool
    {
        return $this->comparator->lessThan($money, $other);
    }

    /**
     * @param MoneyInterface $money
     * @param MoneyInterface $other
     * @return bool
     */
    public function lessThanOrEqual(MoneyInterface $money, MoneyInterface $other): bool
    {
        return $this->comparator->lessThanOrEqual($money, $other);
    }

    /**
     * @param MoneyInterface $first
     * @param MoneyInterface ...$items
     * @return MoneyInterface
     */
    public function max(MoneyInterface $first, MoneyInterface ...$items): MoneyInterface
    {
        $max = $first;

        foreach ($items as $item) {
            if ($this->comparator->greaterThan($item, $max)) {
                $max = $item;
            }
        }
        
        return $max;
    }

    /**
     * @param MoneyInterface $first
     * @param MoneyInterface ...$items
     * @return MoneyInterface
     */
    public function min(MoneyInterface $first, MoneyInterface ...$items): MoneyInterface
    {
        $min = $first;

        foreach ($items as $item) {
            if ($this->comparator->lessThan($item, $min)) {
                $min = $item;
            }
        }

        return $min;
    }

    /**
     * @param MoneyInterface $money
     * @param MoneyInterface ...$items
     * @return MoneyInterface
     */
    public function avg(MoneyInterface $money, MoneyInterface ...$items): MoneyInterface
    {
        $sum = $this->calculator->add($money, ...$items);

        return $this->calculator->divide($sum, \func_num_args());
    }
}

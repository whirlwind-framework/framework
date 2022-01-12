<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Money;

interface MoneyComparatorInterface
{
    /**
     * @param MoneyInterface $money
     * @param MoneyInterface $other
     * @return int
     */
    public function compare(MoneyInterface $money, MoneyInterface $other): int;

    /**
     * @param MoneyInterface $money
     * @param MoneyInterface $other
     * @return bool
     */
    public function isSameCurrency(MoneyInterface $money, MoneyInterface $other): bool;

    /**
     * @param MoneyInterface $money
     * @param MoneyInterface $other
     * @return bool
     */
    public function equals(MoneyInterface $money, MoneyInterface $other): bool;

    /**
     * @param MoneyInterface $money
     * @param MoneyInterface $other
     * @return bool
     */
    public function greaterThan(MoneyInterface $money, MoneyInterface $other): bool;

    /**
     * @param MoneyInterface $money
     * @param MoneyInterface $other
     * @return bool
     */
    public function greaterThanOrEqual(MoneyInterface $money, MoneyInterface $other): bool;

    /**
     * @param MoneyInterface $money
     * @param MoneyInterface $other
     * @return bool
     */
    public function lessThan(MoneyInterface $money, MoneyInterface $other): bool;

    /**
     * @param MoneyInterface $money
     * @param MoneyInterface $other
     * @return bool
     */
    public function lessThanOrEqual(MoneyInterface $money, MoneyInterface $other): bool;
}

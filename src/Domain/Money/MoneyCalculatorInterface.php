<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Money;

interface MoneyCalculatorInterface
{
    /**
     * @param MoneyInterface $money
     * @param MoneyInterface ...$addends
     * @return MoneyInterface
     */
    public function add(MoneyInterface $money, MoneyInterface ...$addends): MoneyInterface;

    /**
     * @param MoneyInterface $minuend
     * @param MoneyInterface ...$subtrahends
     * @return MoneyInterface
     */
    public function subtract(MoneyInterface $minuend, MoneyInterface ...$subtrahends): MoneyInterface;

    /**
     * @param MoneyInterface $money
     * @param int|float|string $multiplier
     * @param int $mode
     * @return MoneyInterface
     */
    public function multiply(MoneyInterface $money, $multiplier, int $mode = PHP_ROUND_HALF_UP): MoneyInterface;

    /**
     * @param MoneyInterface $money
     * @param int|float|string $divisor
     * @param int $mode
     * @return MoneyInterface
     */
    public function divide(MoneyInterface $money, $divisor, int $mode = PHP_ROUND_HALF_UP): MoneyInterface;
}

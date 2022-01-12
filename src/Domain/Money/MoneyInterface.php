<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Money;

interface MoneyInterface
{
    /**
     * @return string
     */
    public function getAmount(): string;

    /**
     * @return CurrencyInterface
     */
    public function getCurrency(): CurrencyInterface;

    /**
     * @return string
     */
    public function formatAsDecimal(): string;

    /**
     * @return bool
     */
    public function isNegative(): bool;

    /**
     * @return bool
     */
    public function isPositive(): bool;

    /**
     * @return bool
     */
    public function isZero(): bool;

    /**
     * @return MoneyInterface
     */
    public function setNegative(): MoneyInterface;

    /**
     * @return MoneyInterface
     */
    public function setAbsolute(): MoneyInterface;
}

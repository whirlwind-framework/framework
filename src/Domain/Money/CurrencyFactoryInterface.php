<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Money;

interface CurrencyFactoryInterface
{
    /**
     * @param string $currency
     * @return CurrencyInterface
     */
    public function create(string $currency): CurrencyInterface;
}

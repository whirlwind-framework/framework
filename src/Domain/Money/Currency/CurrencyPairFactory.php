<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Money\Currency;

use Whirlwind\Domain\Money\CurrencyInterface;

class CurrencyPairFactory
{
    public function create(
        CurrencyInterface $baseCurrency,
        CurrencyInterface $targetCurrency,
        float $ratio
    ): CurrencyPair {
        return new CurrencyPair(
            $baseCurrency,
            $targetCurrency,
            $ratio
        );
    }
}

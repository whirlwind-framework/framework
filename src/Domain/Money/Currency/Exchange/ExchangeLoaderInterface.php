<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Money\Currency\Exchange;

use Whirlwind\Domain\Money\Currency\CurrencyPairCollection;
use Whirlwind\Domain\Money\CurrencyInterface;

interface ExchangeLoaderInterface
{
    /**
     * @param CurrencyInterface $baseCurrency
     * @param CurrencyInterface ...$targets
     * @return CurrencyPairCollection
     */
    public function load(CurrencyInterface $baseCurrency, CurrencyInterface ...$targets): CurrencyPairCollection;
}

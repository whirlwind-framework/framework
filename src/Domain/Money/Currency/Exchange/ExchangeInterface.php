<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Money\Currency\Exchange;

use Whirlwind\Domain\Money\Currency\CurrencyPair;
use Whirlwind\Domain\Money\CurrencyInterface;

interface ExchangeInterface
{
    /**
     * @param CurrencyInterface $baseCurrency
     * @param CurrencyInterface $counterCurrency
     * @return CurrencyPair
     */
    public function quote(CurrencyInterface $baseCurrency, CurrencyInterface $counterCurrency): CurrencyPair;
}

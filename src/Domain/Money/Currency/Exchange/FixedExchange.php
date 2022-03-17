<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Money\Currency\Exchange;

use Whirlwind\Domain\Money\Currency\CurrencyPair;
use Whirlwind\Domain\Money\Currency\CurrencyPairCollection;
use Whirlwind\Domain\Money\Currency\Exchange\Exception\UnresolvableCurrencyPairException;
use Whirlwind\Domain\Money\CurrencyInterface;

class FixedExchange implements ExchangeInterface
{
    /**
     * @var CurrencyPairCollection
     */
    private $pairs;

    /**
     * @param CurrencyPairCollection $pairs
     */
    public function __construct(CurrencyPairCollection $pairs)
    {
        $this->pairs = $pairs;
    }

    /**
     * @param CurrencyInterface $baseCurrency
     * @param CurrencyInterface $counterCurrency
     * @return CurrencyPair
     * @throws UnresolvableCurrencyPairException
     */
    public function quote(CurrencyInterface $baseCurrency, CurrencyInterface $counterCurrency): CurrencyPair
    {
        $pair = $this->pairs->findByBaseAndTarget($baseCurrency, $counterCurrency);

        if (null === $pair) {
            throw UnresolvableCurrencyPairException::createFromCurrencies($baseCurrency, $counterCurrency);
        }

        return $pair;
    }

    /**
     * @return CurrencyPairCollection
     */
    public function getPairs(): CurrencyPairCollection
    {
        return $this->pairs;
    }
}

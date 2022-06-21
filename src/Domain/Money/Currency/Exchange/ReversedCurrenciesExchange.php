<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Money\Currency\Exchange;

use Whirlwind\Domain\Money\Currency\CurrencyPair;
use Whirlwind\Domain\Money\Currency\CurrencyPairCollection;
use Whirlwind\Domain\Money\Currency\Exchange\Exception\UnresolvableCurrencyPairException;
use Whirlwind\Domain\Money\CurrencyInterface;

class ReversedCurrenciesExchange implements ExchangeInterface
{
    /**
     * @var CurrencyPairCollection
     */
    private CurrencyPairCollection $pairs;

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
            $pair = $this->pairs->findByBaseAndTarget($counterCurrency, $baseCurrency);
            if (null !== $pair) {
                $pair = new CurrencyPair(
                    $baseCurrency,
                    $counterCurrency,
                    1 / $pair->getBaseToTargetRatio()
                );
            }
        }

        if (null === $pair) {
            throw UnresolvableCurrencyPairException::createFromCurrencies($baseCurrency, $counterCurrency);
        }

        return $pair;
    }

    /**
     * @param CurrencyPair $pair
     * @return void
     */
    public function updateExchangeRate(CurrencyPair $pair): void
    {
        $reversedExchangePair = $this->pairs->findByBaseAndTarget($pair->getTarget(), $pair->getBase());

        if ($reversedExchangePair) {
            $this->pairs->addUniquePair(new CurrencyPair(
                $pair->getTarget(),
                $pair->getBase(),
                $pair->getBaseToTargetRatio()
            ));
        } else {
            $this->pairs->addUniquePair($pair);
        }
    }

    /**
     * @return CurrencyPairCollection
     */
    public function getPairs(): CurrencyPairCollection
    {
        return $this->pairs;
    }
}

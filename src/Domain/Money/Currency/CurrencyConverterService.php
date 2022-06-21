<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Money\Currency;

use Whirlwind\Domain\Money\Currency\Exchange\ExchangeInterface;
use Whirlwind\Domain\Money\CurrencyInterface;
use Whirlwind\Domain\Money\MoneyCalculatorInterface;
use Whirlwind\Domain\Money\MoneyFactoryInterface;
use Whirlwind\Domain\Money\MoneyInterface;

class CurrencyConverterService
{
    /**
     * @var ExchangeInterface
     */
    protected $exchange;
    /**
     * @var MoneyCalculatorInterface
     */
    protected $moneyCalculator;
    /**
     * @var MoneyFactoryInterface
     */
    protected $moneyFactory;

    /**
     * @param ExchangeInterface $exchange
     * @param MoneyCalculatorInterface $moneyCalculator
     * @param MoneyFactoryInterface $moneyFactory
     */
    public function __construct(
        ExchangeInterface $exchange,
        MoneyCalculatorInterface $moneyCalculator,
        MoneyFactoryInterface $moneyFactory
    ) {
        $this->exchange = $exchange;
        $this->moneyCalculator = $moneyCalculator;
        $this->moneyFactory = $moneyFactory;
    }

    /**
     * @param MoneyInterface $money
     * @param CurrencyInterface $counterCurrency
     * @param int $roundingMode
     * @return MoneyInterface
     */
    public function convert(
        MoneyInterface $money,
        CurrencyInterface $counterCurrency,
        int $roundingMode = PHP_ROUND_HALF_UP
    ): MoneyInterface {
        if ($money->getCurrency()->equals($counterCurrency)) {
            return $money;
        }

        $pair = $this->exchange->quote($money->getCurrency(), $counterCurrency);

        $money = $this->moneyCalculator->multiply($money, $pair->getBaseToTargetRatio(), $roundingMode);

        return $this->moneyFactory->create($money->getAmount(), $counterCurrency->getCode());
    }

    /**
     * @param CurrencyPair $currencyPair
     * @return void
     */
    public function updateExchangeRate(CurrencyPair $currencyPair): void
    {
        $this->exchange->updateExchangeRate($currencyPair);
    }
}

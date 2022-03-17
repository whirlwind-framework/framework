<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Money\Currency;

use Whirlwind\Domain\Money\Currency\Exchange\ExchangeFactory;
use Whirlwind\Domain\Money\Currency\Exchange\ExchangeLoaderInterface;
use Whirlwind\Domain\Money\CurrencyInterface;
use Whirlwind\Domain\Money\MoneyCalculatorInterface;
use Whirlwind\Domain\Money\MoneyFactoryInterface;
use Whirlwind\Domain\Money\MoneyInterface;

class CurrencyConverterService
{
    /**
     * @var ExchangeLoaderInterface
     */
    protected $exchangeLoader;

    /**
     * @var ExchangeFactory
     */
    protected $exchangeFactory;
    /**
     * @var MoneyCalculatorInterface
     */
    protected $moneyCalculator;
    /**
     * @var MoneyFactoryInterface
     */
    protected $moneyFactory;

    /**
     * @param ExchangeLoaderInterface $exchangeLoader
     * @param ExchangeFactory $exchangeFactory
     * @param MoneyCalculatorInterface $moneyCalculator
     * @param MoneyFactoryInterface $moneyFactory
     */
    public function __construct(
        ExchangeLoaderInterface $exchangeLoader,
        ExchangeFactory $exchangeFactory,
        MoneyCalculatorInterface $moneyCalculator,
        MoneyFactoryInterface $moneyFactory
    ) {
        $this->exchangeLoader = $exchangeLoader;
        $this->exchangeFactory = $exchangeFactory;
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

        $pairs = $this->exchangeLoader->load($money->getCurrency(), $counterCurrency);
        $exchange = $this->exchangeFactory->create($pairs);

        $pair = $exchange->quote($money->getCurrency(), $counterCurrency);

        $money = $this->moneyCalculator->multiply($money, $pair->getBaseToTargetRatio(), $roundingMode);

        return $this->moneyFactory->create($money->getAmount(), $counterCurrency->getCode());
    }
}

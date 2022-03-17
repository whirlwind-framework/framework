<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Money\Currency\Exchange;

use Whirlwind\Domain\Money\Currency\CurrencyPairCollection;

class ExchangeFactory
{
    public const TYPE_FIXED = 'fixed';
    public const TYPE_REVERSED_CURRENCIES = 'reversedCurrencies';
    /**
     * @var string
     */
    protected $type = self::TYPE_FIXED;

    /**
     * @param string $type
     */
    public function __construct(string $type = self::TYPE_FIXED)
    {
        $this->type = $type;
    }

    /**
     * @param CurrencyPairCollection $pairs
     * @return ExchangeInterface
     */
    public function create(CurrencyPairCollection $pairs): ExchangeInterface
    {
        $method = \sprintf('create%sExchange', \ucfirst($this->type));

        if (!\method_exists($this, $method)) {
            throw new \InvalidArgumentException("Factory does not have '$method' method");
        }

        return $this->$method($pairs);
    }

    /**
     * @param CurrencyPairCollection $pairs
     * @return FixedExchange
     */
    protected function createFixedExchange(CurrencyPairCollection $pairs): FixedExchange
    {
        return new FixedExchange($pairs);
    }

    /**
     * @param CurrencyPairCollection $pairs
     * @return ReversedCurrenciesExchange
     */
    protected function createReversedCurrenciesExchange(CurrencyPairCollection $pairs): ReversedCurrenciesExchange
    {
        return new ReversedCurrenciesExchange($pairs);
    }
}

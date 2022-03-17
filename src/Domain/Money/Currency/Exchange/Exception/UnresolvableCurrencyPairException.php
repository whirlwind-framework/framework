<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Money\Currency\Exchange\Exception;

use Whirlwind\Domain\Money\CurrencyInterface;

class UnresolvableCurrencyPairException extends \Exception
{
    /**
     * @param CurrencyInterface $baseCurrency
     * @param CurrencyInterface $counterCurrency
     * @return UnresolvableCurrencyPairException
     */
    public static function createFromCurrencies(
        CurrencyInterface $baseCurrency,
        CurrencyInterface $counterCurrency
    ): self {
        $message = \sprintf(
            'Cannot resolve a currency pair for currencies: %s/%s',
            $baseCurrency->getCode(),
            $counterCurrency->getCode()
        );

        return new self($message);
    }
}

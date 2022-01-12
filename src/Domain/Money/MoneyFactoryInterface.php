<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Money;

interface MoneyFactoryInterface
{
    /**
     * @param string $amount
     * @param string $currencyCode
     * @return MoneyInterface
     */
    public function create(string $amount, string $currencyCode): MoneyInterface;

    /**
     * @param string $amount
     * @param string $currencyCode
     * @return MoneyInterface
     */
    public function __invoke(string $amount, string $currencyCode): MoneyInterface;
}

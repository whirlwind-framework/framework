<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Money;

interface MoneyInterface
{
    public function getAmount(): string;

    public function getCurrency(): CurrencyInterface;
}

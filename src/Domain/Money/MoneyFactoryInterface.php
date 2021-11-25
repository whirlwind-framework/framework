<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Money;

interface MoneyFactoryInterface
{
    public function create(string $amount, string $currencyCode): MoneyInterface;
}

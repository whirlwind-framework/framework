<?php declare(strict_types=1);

namespace Whirlwind\Domain\Money;

interface CurrencyInterface
{
    public function getCode(): string;

    public function equals(CurrencyInterface $other): bool;
}

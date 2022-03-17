<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Money\Currency;

use Whirlwind\Domain\Money\CurrencyInterface;

final class CurrencyPair
{
    /**
     * @var CurrencyInterface
     */
    private $base;
    /**
     * @var CurrencyInterface
     */
    private $target;
    /**
     * @var float
     */
    private $baseToTargetRatio;

    /**
     * @param CurrencyInterface $base
     * @param CurrencyInterface $target
     * @param float $baseToTargetRatio
     */
    public function __construct(CurrencyInterface $base, CurrencyInterface $target, float $baseToTargetRatio)
    {
        $this->base = $base;
        $this->target = $target;
        $this->baseToTargetRatio = $baseToTargetRatio;
    }

    /**
     * @return CurrencyInterface
     */
    public function getBase(): CurrencyInterface
    {
        return $this->base;
    }

    /**
     * @return CurrencyInterface
     */
    public function getTarget(): CurrencyInterface
    {
        return $this->target;
    }

    /**
     * @return float
     */
    public function getBaseToTargetRatio(): float
    {
        return $this->baseToTargetRatio;
    }
}

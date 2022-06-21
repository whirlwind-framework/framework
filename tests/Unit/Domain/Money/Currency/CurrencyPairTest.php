<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Money\Currency;

use DG\BypassFinals;
use PHPUnit\Framework\TestCase;
use Whirlwind\Domain\Money\Currency\CurrencyPair;
use Whirlwind\Domain\Money\CurrencyInterface;

class CurrencyPairTest extends TestCase
{
    private $base;
    private $target;
    private $baseToTargetRatio = 1.1;
    private $entity;

    protected function setUp(): void
    {
        BypassFinals::enable();
        parent::setUp();

        $this->base = $this->createMock(CurrencyInterface::class);
        $this->target = $this->createMock(CurrencyInterface::class);

        $this->entity = new CurrencyPair($this->base, $this->target, $this->baseToTargetRatio);
    }

    public function testGetBase()
    {
        self::assertSame($this->base, $this->entity->getBase());
    }

    public function testGetBaseToTargetRatio()
    {
        self::assertSame($this->baseToTargetRatio, $this->entity->getBaseToTargetRatio());
    }

    public function testGetTarget()
    {
        self::assertSame($this->target, $this->entity->getTarget());
    }
}

<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Money\Currency;

use DG\BypassFinals;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Whirlwind\Domain\Money\Currency\CurrencyConverterService;
use Whirlwind\Domain\Money\Currency\CurrencyPair;
use Whirlwind\Domain\Money\Currency\Exchange\ExchangeInterface;
use Whirlwind\Domain\Money\CurrencyInterface;
use Whirlwind\Domain\Money\MoneyCalculatorInterface;
use Whirlwind\Domain\Money\MoneyFactoryInterface;
use Whirlwind\Domain\Money\MoneyInterface;

class CurrencyConverterServiceTest extends TestCase
{
    private $exchange;
    private $moneyCalculator;
    private $moneyFactory;
    private $converter;

    protected function setUp(): void
    {
        BypassFinals::enable();
        parent::setUp();

        $this->exchange = $this->createMock(ExchangeInterface::class);
        $this->moneyCalculator = $this->createMock(MoneyCalculatorInterface::class);
        $this->moneyFactory = $this->createMock(MoneyFactoryInterface::class);

        $this->converter = new CurrencyConverterService(
            $this->exchange,
            $this->moneyCalculator,
            $this->moneyFactory
        );
    }

    public function testConvert()
    {
        $base = $this->createMock(CurrencyInterface::class);
        $money = $this->createMoneyMock($base);
        $counterCurrency = $this->createMock(CurrencyInterface::class);

        $base->expects(self::once())
            ->method('equals')
            ->with(self::identicalTo($counterCurrency))
            ->willReturn(false);

        $pair = $this->createMock(CurrencyPair::class);
        $this->exchange->expects(self::once())
            ->method('quote')
            ->with(self::identicalTo($base), self::identicalTo($counterCurrency))
            ->willReturn($pair);

        $ratio = 1.0;
        $pair->expects(self::once())
            ->method('getBaseToTargetRatio')
            ->willReturn($ratio);

        $this->moneyCalculator->expects(self::once())
            ->method('multiply')
            ->with(self::identicalTo($money), self::identicalTo($ratio))
            ->willReturn($money);

        $money->expects(self::once())
            ->method('getAmount')
            ->willReturn('100000');

        $counterCurrency->expects(self::once())
            ->method('getCode')
            ->willReturn('EUR');

        $newMoney = $this->createMoneyMock($counterCurrency);
        $this->moneyFactory->expects(self::once())
            ->method('create')
            ->with(self::identicalTo('100000'), self::equalTo('EUR'))
            ->willReturn($newMoney);

        $actual = $this->converter->convert($money, $counterCurrency);
        self::assertSame($newMoney, $actual);
    }

    /**
     * @param CurrencyInterface $currency
     * @return MoneyInterface&MockObject
     */
    private function createMoneyMock(CurrencyInterface $currency): MoneyInterface
    {
        $entity = $this->createMock(MoneyInterface::class);

        $entity->expects(self::any())
            ->method('getCurrency')
            ->willReturn($currency);

        return $entity;
    }
}

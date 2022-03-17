<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Money\Currency;

use DG\BypassFinals;
use Whirlwind\Domain\Money\Currency\CurrencyConverterService;
use Whirlwind\Domain\Money\Currency\CurrencyPair;
use Whirlwind\Domain\Money\Currency\CurrencyPairCollection;
use Whirlwind\Domain\Money\Currency\Exchange\ExchangeFactory;
use Whirlwind\Domain\Money\Currency\Exchange\ExchangeInterface;
use Whirlwind\Domain\Money\Currency\Exchange\ExchangeLoaderInterface;
use Whirlwind\Domain\Money\CurrencyInterface;
use Whirlwind\Domain\Money\MoneyCalculatorInterface;
use Whirlwind\Domain\Money\MoneyFactoryInterface;
use Whirlwind\Domain\Money\MoneyInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CurrencyConverterServiceTest extends TestCase
{
    private $exchangeLoader;
    private $exchangeFactory;
    private $moneyCalculator;
    private $moneyFactory;
    private $converter;

    protected function setUp(): void
    {
        BypassFinals::enable();
        parent::setUp();

        $this->exchangeLoader = $this->createMock(ExchangeLoaderInterface::class);
        $this->exchangeFactory = $this->createMock(ExchangeFactory::class);
        $this->moneyCalculator = $this->createMock(MoneyCalculatorInterface::class);
        $this->moneyFactory = $this->createMock(MoneyFactoryInterface::class);

        $this->converter = new CurrencyConverterService(
            $this->exchangeLoader,
            $this->exchangeFactory,
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

        $pairs = $this->createMock(CurrencyPairCollection::class);
        $this->exchangeLoader->expects(self::once())
            ->method('load')
            ->with(self::identicalTo($base), self::identicalTo($counterCurrency))
            ->willReturn($pairs);

        $exchange = $this->createMock(ExchangeInterface::class);
        $this->exchangeFactory->expects(self::once())
            ->method('create')
            ->with(self::identicalTo($pairs))
            ->willReturn($exchange);

        $pair = $this->createMock(CurrencyPair::class);
        $exchange->expects(self::once())
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

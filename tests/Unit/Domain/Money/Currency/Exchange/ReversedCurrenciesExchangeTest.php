<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Money\Currency\Exchange;

use DG\BypassFinals;
use PHPUnit\Framework\TestCase;
use Whirlwind\Domain\Money\Currency\CurrencyPair;
use Whirlwind\Domain\Money\Currency\CurrencyPairCollection;
use Whirlwind\Domain\Money\Currency\Exchange\Exception\UnresolvableCurrencyPairException;
use Whirlwind\Domain\Money\Currency\Exchange\ReversedCurrenciesExchange;
use Whirlwind\Domain\Money\CurrencyInterface;

class ReversedCurrenciesExchangeTest extends TestCase
{
    private $pairs;
    private $exchange;

    protected function setUp(): void
    {
        BypassFinals::enable();
        parent::setUp();

        $this->pairs = $this->createMock(CurrencyPairCollection::class);
        $this->exchange = new ReversedCurrenciesExchange($this->pairs);
    }

    public function testQuote()
    {
        $baseCurrency = $this->createMock(CurrencyInterface::class);
        $counterCurrency = $this->createMock(CurrencyInterface::class);

        $pair = $this->createMock(CurrencyPair::class);
        $this->pairs->expects(self::exactly(2))
            ->method('findByBaseAndTarget')
            ->withConsecutive(
                [self::identicalTo($baseCurrency), self::identicalTo($counterCurrency)],
                [self::identicalTo($counterCurrency), self::identicalTo($baseCurrency)]
            )
            ->willReturnOnConsecutiveCalls(null, $pair);

        $pair->expects(self::once())
            ->method('getBaseToTargetRatio')
            ->willReturn(2.0);

        $actual = $this->exchange->quote($baseCurrency, $counterCurrency);
        self::assertSame($baseCurrency, $actual->getBase());
        self::assertSame($counterCurrency, $actual->getTarget());
        self::assertSame(0.5, $actual->getBaseToTargetRatio());
    }

    public function testQuoteException()
    {
        $baseCurrency = $this->createMock(CurrencyInterface::class);
        $counterCurrency = $this->createMock(CurrencyInterface::class);

        $this->pairs->expects(self::exactly(2))
            ->method('findByBaseAndTarget')
            ->withConsecutive(
                [self::identicalTo($baseCurrency), self::identicalTo($counterCurrency)],
                [self::identicalTo($counterCurrency), self::identicalTo($baseCurrency)]
            )
            ->willReturnOnConsecutiveCalls(null, null);

        $baseCurrency->expects(self::once())
            ->method('getCode')
            ->willReturn('USD');

        $counterCurrency->expects(self::once())
            ->method('getCode')
            ->willReturn('UAH');

        $this->expectException(UnresolvableCurrencyPairException::class);
        $this->expectExceptionMessage('Cannot resolve a currency pair for currencies: USD/UAH');
        $this->exchange->quote($baseCurrency, $counterCurrency);
    }

    public function testGetPairs()
    {
        self::assertSame($this->pairs, $this->exchange->getPairs());
    }
}

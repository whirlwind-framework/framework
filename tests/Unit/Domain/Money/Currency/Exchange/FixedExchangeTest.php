<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Money\Currency\Exchange;

use DG\BypassFinals;
use Whirlwind\Domain\Money\Currency\CurrencyPair;
use Whirlwind\Domain\Money\Currency\CurrencyPairCollection;
use Whirlwind\Domain\Money\Currency\Exchange\Exception\UnresolvableCurrencyPairException;
use Whirlwind\Domain\Money\Currency\Exchange\FixedExchange;
use Whirlwind\Domain\Money\CurrencyInterface;
use PHPUnit\Framework\TestCase;

class FixedExchangeTest extends TestCase
{
    private $pairs;
    private $exchange;

    protected function setUp(): void
    {
        BypassFinals::enable();
        parent::setUp();

        $this->pairs = $this->createMock(CurrencyPairCollection::class);
        $this->exchange = new FixedExchange($this->pairs);
    }

    public function testQuote()
    {
        $baseCurrency = $this->createMock(CurrencyInterface::class);
        $counterCurrency = $this->createMock(CurrencyInterface::class);

        $pair = $this->createMock(CurrencyPair::class);
        $this->pairs->expects(self::once())
            ->method('findByBaseAndTarget')
            ->with(self::identicalTo($baseCurrency), self::identicalTo($counterCurrency))
            ->willReturn($pair);

        $actual = $this->exchange->quote($baseCurrency, $counterCurrency);
        self::assertSame($pair, $actual);
    }

    public function testQuoteException()
    {
        $baseCurrency = $this->createMock(CurrencyInterface::class);
        $counterCurrency = $this->createMock(CurrencyInterface::class);

        $this->pairs->expects(self::once())
            ->method('findByBaseAndTarget')
            ->with(self::identicalTo($baseCurrency), self::identicalTo($counterCurrency))
            ->willReturn(null);

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

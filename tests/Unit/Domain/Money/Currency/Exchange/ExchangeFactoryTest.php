<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Money\Currency\Exchange;

use DG\BypassFinals;
use Whirlwind\Domain\Money\Currency\CurrencyPairCollection;
use Whirlwind\Domain\Money\Currency\Exchange\ExchangeFactory;
use Whirlwind\Domain\Money\Currency\Exchange\FixedExchange;
use Whirlwind\Domain\Money\Currency\Exchange\ReversedCurrenciesExchange;
use PHPUnit\Framework\TestCase;

class ExchangeFactoryTest extends TestCase
{
    private $factory;

    protected function setUp(): void
    {
        BypassFinals::enable();
        parent::setUp();

        $this->factory = new ExchangeFactory();
    }

    public function testCreate()
    {
        $pairs = $this->createMock(CurrencyPairCollection::class);

        $actual = $this->factory->create($pairs);
        self::assertInstanceOf(FixedExchange::class, $actual);
    }

    public function testCreatedReversedExchange()
    {
        $pairs = $this->createMock(CurrencyPairCollection::class);

        $factory = new ExchangeFactory(ExchangeFactory::TYPE_REVERSED_CURRENCIES);
        $actual = $factory->create($pairs);
        self::assertInstanceOf(ReversedCurrenciesExchange::class, $actual);
    }

    public function testUnknownExchange()
    {
        $pairs = $this->createMock(CurrencyPairCollection::class);

        $factory = new ExchangeFactory('unknown');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Factory does not have 'createUnknownExchange' method");

        $factory->create($pairs);
    }
}

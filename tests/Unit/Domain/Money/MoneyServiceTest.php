<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Money;

use DG\BypassFinals;
use PHPUnit\Framework\TestCase;
use Whirlwind\Domain\Money\MoneyCalculatorInterface;
use Whirlwind\Domain\Money\MoneyComparatorInterface;
use Whirlwind\Domain\Money\MoneyInterface;
use Whirlwind\Domain\Money\MoneyService;

class MoneyServiceTest extends TestCase
{
    private $comparator;
    private $calculator;

    private $service;

    protected function setUp(): void
    {
        BypassFinals::enable();
        parent::setUp();

        $this->comparator = $this->createMock(MoneyComparatorInterface::class);
        $this->calculator = $this->createMock(MoneyCalculatorInterface::class);

        $this->service = new MoneyService(
            $this->comparator,
            $this->calculator
        );
    }

    /**
     * @param $multiplier
     * @return void
     *
     * @dataProvider multiplyDataProvider
     */
    public function testMultiply($multiplier)
    {
        $money = $this->createMock(MoneyInterface::class);

        $this->calculator->expects(self::once())
            ->method('multiply')
            ->with(self::identicalTo($money), self::identicalTo($multiplier), self::equalTo(PHP_ROUND_HALF_UP))
            ->willReturn($money);

        $actual = $this->service->multiply($money, $multiplier);
        self::assertSame($money, $actual);
    }

    public function multiplyDataProvider(): array
    {
        return [
            ['multiplier' => 10],
            ['multiplier' => 2.2],
            ['multiplier' => '11'],
        ];
    }

    public function testMin()
    {
        $first = $this->createMock(MoneyInterface::class);
        $expected = $this->createMock(MoneyInterface::class);

        $this->comparator->expects(self::once())
            ->method('lessThan')
            ->with(self::identicalTo($expected), self::identicalTo($first))
            ->willReturn(true);

        $actual = $this->service->min($first, $expected);
        self::assertSame($expected, $actual);
    }

    public function testAdd()
    {
        $money = $this->createMock(MoneyInterface::class);
        $addend = $this->createMock(MoneyInterface::class);

        $expected = $this->createMock(MoneyInterface::class);
        $this->calculator->expects(self::once())
            ->method('add')
            ->with(self::identicalTo($money), self::identicalTo($addend))
            ->willReturn($expected);

        $actual = $this->service->add($money, $addend);
        self::assertSame($expected, $actual);
    }

    public function testEquals()
    {
        $money = $this->createMock(MoneyInterface::class);
        $other = $this->createMock(MoneyInterface::class);
        $this->comparator->expects(self::once())
            ->method('equals')
            ->with(self::identicalTo($money), self::identicalTo($other))
            ->willReturn(true);

        self::assertTrue($this->service->equals($money, $other));
    }

    public function testLessThan()
    {
        $money = $this->createMock(MoneyInterface::class);
        $other = $this->createMock(MoneyInterface::class);

        $this->comparator->expects(self::exactly(3))
            ->method('lessThan')
            ->withConsecutive(
                [self::identicalTo($money), self::identicalTo($other)],
                [self::identicalTo($money), self::identicalTo($money)],
                [self::identicalTo($other), self::identicalTo($money)]
            )
            ->willReturnOnConsecutiveCalls(true, false, false);

        self::assertTrue($this->comparator->lessThan($money, $other));
        self::assertFalse($this->comparator->lessThan($money, $money));
        self::assertFalse($this->comparator->lessThan($other, $money));
    }

    public function testIsSameCurrency()
    {
        $money = $this->createMock(MoneyInterface::class);
        $other = $this->createMock(MoneyInterface::class);

        $this->comparator->expects(self::exactly(2))
            ->method('isSameCurrency')
            ->withConsecutive(
                [self::identicalTo($money), self::identicalTo($money)],
                [self::identicalTo($money), self::identicalTo($other)],
            )
            ->willReturnOnConsecutiveCalls(true, false);
        self::assertTrue($this->service->isSameCurrency($money, $money));
        self::assertFalse($this->service->isSameCurrency($money, $other));
    }

    public function testSubtract()
    {
        $money = $this->createMock(MoneyInterface::class);
        $subtrahend = $this->createMock(MoneyInterface::class);

        $expected = $this->createMock(MoneyInterface::class);
        $this->calculator->expects(self::once())
            ->method('subtract')
            ->with(self::identicalTo($money), self::identicalTo($subtrahend))
            ->willReturn($expected);

        $actual = $this->service->subtract($money, $subtrahend);
        self::assertSame($expected, $actual);
    }

    public function testCompare()
    {
        $money = $this->createMock(MoneyInterface::class);
        $other = $this->createMock(MoneyInterface::class);

        $this->comparator->expects(self::exactly(3))
            ->method('compare')
            ->withConsecutive(
                [self::identicalTo($money), self::identicalTo($other)],
                [self::identicalTo($money), self::identicalTo($money)],
                [self::identicalTo($other), self::identicalTo($money)],
            )
            ->willReturnOnConsecutiveCalls(1, 0, -1);

        self::assertEquals(1, $this->comparator->compare($money, $other));
        self::assertEquals(0, $this->comparator->compare($money, $money));
        self::assertEquals(-1, $this->comparator->compare($other, $money));
    }

    public function testGreaterThanOrEqual()
    {
        $money = $this->createMock(MoneyInterface::class);
        $other = $this->createMock(MoneyInterface::class);

        $this->comparator->expects(self::exactly(3))
            ->method('greaterThanOrEqual')
            ->withConsecutive(
                [self::identicalTo($money), self::identicalTo($other)],
                [self::identicalTo($money), self::identicalTo($money)],
                [self::identicalTo($other), self::identicalTo($money)]
            )
            ->willReturnOnConsecutiveCalls(true, true, false);

        self::assertTrue($this->comparator->greaterThanOrEqual($money, $other));
        self::assertTrue($this->comparator->greaterThanOrEqual($money, $money));
        self::assertFalse($this->comparator->greaterThanOrEqual($other, $money));
    }

    public function testLessThanOrEqual()
    {
        $money = $this->createMock(MoneyInterface::class);
        $other = $this->createMock(MoneyInterface::class);

        $this->comparator->expects(self::exactly(3))
            ->method('lessThanOrEqual')
            ->withConsecutive(
                [self::identicalTo($money), self::identicalTo($other)],
                [self::identicalTo($money), self::identicalTo($money)],
                [self::identicalTo($other), self::identicalTo($money)]
            )
            ->willReturnOnConsecutiveCalls(true, true, false);

        self::assertTrue($this->comparator->lessThanOrEqual($money, $other));
        self::assertTrue($this->comparator->lessThanOrEqual($money, $money));
        self::assertFalse($this->comparator->lessThanOrEqual($other, $money));
    }

    public function testAvg()
    {
        $money = $this->createMock(MoneyInterface::class);
        $item = $this->createMock(MoneyInterface::class);
        $sum = $this->createMock(MoneyInterface::class);

        $this->calculator->expects(self::once())
            ->method('add')
            ->with(self::identicalTo($money), self::identicalTo($item))
            ->willReturn($sum);

        $expected = $this->createMock(MoneyInterface::class);
        $this->calculator->expects(self::once())
            ->method('divide')
            ->with(self::identicalTo($sum), self::equalTo(2))
            ->willReturn($expected);

        $actual = $this->service->avg($money, $item);
        self::assertSame($expected, $actual);
    }

    public function testMax()
    {
        $first = $this->createMock(MoneyInterface::class);
        $expected = $this->createMock(MoneyInterface::class);

        $this->comparator->expects(self::once())
            ->method('greaterThan')
            ->with(self::identicalTo($expected), self::identicalTo($first))
            ->willReturn(true);

        $actual = $this->service->max($first, $expected);
        self::assertSame($expected, $actual);
    }

    /**
     * @param $divisor
     * @return void
     * @dataProvider divideDataProvider
     */
    public function testDivide($divisor)
    {
        $money = $this->createMock(MoneyInterface::class);

        $this->calculator->expects(self::once())
            ->method('divide')
            ->with(self::identicalTo($money), self::identicalTo($divisor), self::equalTo(PHP_ROUND_HALF_UP))
            ->willReturn($money);

        $actual = $this->service->divide($money, $divisor);
        self::assertSame($money, $actual);
    }

    public function divideDataProvider(): array
    {
        return [
            ['multiplier' => 10],
            ['multiplier' => 2.2],
            ['multiplier' => '11'],
        ];
    }

    public function testGreaterThan()
    {
        $money = $this->createMock(MoneyInterface::class);
        $other = $this->createMock(MoneyInterface::class);

        $this->comparator->expects(self::exactly(3))
            ->method('greaterThan')
            ->withConsecutive(
                [self::identicalTo($money), self::identicalTo($other)],
                [self::identicalTo($money), self::identicalTo($money)],
                [self::identicalTo($other), self::identicalTo($money)]
            )
            ->willReturnOnConsecutiveCalls(true, false, false);

        self::assertTrue($this->comparator->greaterThan($money, $other));
        self::assertFalse($this->comparator->greaterThan($money, $money));
        self::assertFalse($this->comparator->greaterThan($other, $money));
    }
}

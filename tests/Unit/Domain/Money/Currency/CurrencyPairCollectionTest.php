<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Money\Currency;

use DG\BypassFinals;
use PHPUnit\Framework\TestCase;
use Test\Util\CollectionMockable;
use Whirlwind\Domain\Money\Currency\CurrencyPair;
use Whirlwind\Domain\Money\Currency\CurrencyPairCollection;
use Whirlwind\Domain\Money\CurrencyInterface;

class CurrencyPairCollectionTest extends TestCase
{
    use CollectionMockable;

    private $collection;

    protected function setUp(): void
    {
        BypassFinals::enable();
        parent::setUp();
        $this->collection = new CurrencyPairCollection();
    }

    public function testAddEntity(): void
    {
        $entity = $this->createMock(CurrencyPair::class);
        $this->collection->add($entity);
        $this->assertCount(1, $this->collection);
        $this->assertInstanceOf(CurrencyPair::class, $this->collection[0]);
    }

    public function testAddInvalidEntity(): void
    {
        $entity = new \stdClass();
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid object provided. Expected: ' . CurrencyPair::class);
        $this->collection->add($entity);
    }
    public function testMerge()
    {
        $base = $this->createMock(CurrencyInterface::class);
        $target = $this->createMock(CurrencyInterface::class);

        $entity = $this->createCurrencyPairMock($base, $target);
        $this->collection->add($entity);

        $base->expects(self::exactly(2))
            ->method('equals')
            ->with(self::identicalTo($base))
            ->willReturn(true);

        $newTarget = $this->createMock(CurrencyInterface::class);
        $target->expects(self::exactly(2))
            ->method('equals')
            ->withConsecutive(
                [self::identicalTo($target)],
                [self::identicalTo($newTarget)]
            )
            ->willReturnOnConsecutiveCalls(true, false);

        $newEntity = $this->createCurrencyPairMock($base, $newTarget);

        $other = new CurrencyPairCollection([$entity, $newEntity]);

        self::assertCount(2, $this->collection->merge($other));
    }

    private function createCurrencyPairMock(
        CurrencyInterface $base,
        CurrencyInterface $target,
        float $ratio = 1.0
    ): CurrencyPair {
        $entity = $this->createMock(CurrencyPair::class);
        $entity->expects(self::any())
            ->method('getBase')
            ->willReturn($base);

        $entity->expects(self::any())
            ->method('getTarget')
            ->willReturn($target);

        $entity->expects(self::any())
            ->method('getBaseToTargetRatio')
            ->willReturn($ratio);

        return $entity;
    }

    public function testFindByBaseAndTarget()
    {
        $base = $this->createMock(CurrencyInterface::class);
        $target = $this->createMock(CurrencyInterface::class);

        $entity = $this->createCurrencyPairMock($base, $target);
        $this->collection->add($entity);

        $base->expects(self::exactly(2))
            ->method('equals')
            ->with(self::identicalTo($base))
            ->willReturn(true);

        $newTarget = $this->createMock(CurrencyInterface::class);
        $target->expects(self::exactly(2))
            ->method('equals')
            ->withConsecutive(
                [self::identicalTo($target)],
                [self::identicalTo($newTarget)]
            )
            ->willReturnOnConsecutiveCalls(true, false);

        $actual = $this->collection->findByBaseAndTarget($base, $target);
        self::assertSame($entity, $actual);

        self::assertNull($this->collection->findByBaseAndTarget($base, $newTarget));
    }

    public function testAddUniquePair()
    {
        $base = $this->createMock(CurrencyInterface::class);
        $target = $this->createMock(CurrencyInterface::class);
        $entity = $this->createCurrencyPairMock($base, $target);
        $this->collection->add($entity);

        $base->expects(self::exactly(2))
            ->method('equals')
            ->with(self::identicalTo($base))
            ->willReturn(true);

        $newTarget = $this->createMock(CurrencyInterface::class);
        $target->expects(self::exactly(2))
            ->method('equals')
            ->withConsecutive(
                [self::identicalTo($target)],
                [self::identicalTo($newTarget)]
            )
            ->willReturnOnConsecutiveCalls(true, false);
        $this->collection->addUniquePair($entity);
        self::assertCount(1, $this->collection);

        $newEntity = $this->createCurrencyPairMock($base, $newTarget);
        $this->collection->addUniquePair($newEntity);
        self::assertCount(2, $this->collection);
    }
}

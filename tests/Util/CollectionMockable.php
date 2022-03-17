<?php

declare(strict_types=1);

namespace Test\Util;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Whirlwind\Domain\Collection\CollectionInterface;

/**
 * Trait Mockable
 * @package Test\Util
 * @var TestCase $this
 */
trait CollectionMockable
{
    /**
     * @psalm-template RealInstanceType of object
     * @psalm-param class-string<RealInstanceType> $collectionClass
     * @psalm-param array $items
     * @psalm-return MockObject&RealInstanceType
     */
    protected function createCollectionIteratorMock(string $collectionClass, array $items = []): MockObject
    {
        $collection = $this->createMock($collectionClass);
        $this->mockCollectionIterator($collection, $items);
        $this->mockCollectionCountable($collection, \count($items));

        return $collection;
    }

    protected function mockCollectionIterator(CollectionInterface $collection, array $items = []): void
    {
        $collection->expects(self::any())
            ->method('current')
            ->willReturnCallback(static function () use (&$items) {
                return \current($items);
            });

        $collection->expects(self::any())
            ->method('next')
            ->willReturnCallback(static function () use (&$items) {
                return \next($items);
            });

        $collection->expects(self::any())
            ->method('key')
            ->willReturnCallback(static function () use (&$items) {
                return \key($items);
            });

        $collection->expects(self::any())
            ->method('valid')
            ->willReturnCallback(static function () use (&$items) {
                return (bool) \current($items);
            });

        $collection->expects(self::any())
            ->method('rewind')
            ->willReturnCallback(static function () use (&$items) {
                return (bool) \reset($items);
            });

        $collection->expects(self::any())
            ->method('first')
            ->willReturnCallback(static function () use (&$items) {
                return \current($items);
            });
    }

    /**
     * @param CollectionInterface $collection
     * @param int $count
     * @return void
     */
    protected function mockCollectionCountable(CollectionInterface $collection, int $count): void
    {
        $collection->expects(self::any())
            ->method('count')
            ->willReturn($count);
    }
}

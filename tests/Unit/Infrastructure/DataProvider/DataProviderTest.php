<?php

declare(strict_types=1);

namespace Test\Unit\Infrastructure\DataProvider;

use DG\BypassFinals;
use PHPUnit\Framework\MockObject\MockObject;
use Whirlwind\Domain\Repository\RepositoryInterface;
use Whirlwind\Domain\Repository\ResultInterface;
use Whirlwind\Infrastructure\DataProvider\DataProvider;
use PHPUnit\Framework\TestCase;

class DataProviderTest extends TestCase
{
    private MockObject $repository;
    private DataProvider $dataProvider;

    protected function setUp(): void
    {
        BypassFinals::enable();
        parent::setUp();

        $this->repository = $this->createMock(RepositoryInterface::class);
        $this->dataProvider = new DataProvider($this->repository);
    }


    public function testGetModels()
    {
        $this->repository->expects(self::once())
            ->method('aggregateCount')
            ->willReturn(1);

        $result = $this->createMock(ResultInterface::class);
        $this->repository->expects(self::once())
            ->method('findAll')
            ->willReturn($result);

        $expected = [new \stdClass()];
        $this->mockIterator($result, $expected);

        $actual = $this->dataProvider->getModels();
        self::assertSame($expected, $actual);
    }

    private function mockIterator(MockObject $iterator, array $items): void
    {
        $iterator->expects(self::any())
            ->method('current')
            ->willReturnCallback(static function () use (&$items) {
                return \current($items);
            });

        $iterator->expects(self::any())
            ->method('next')
            ->willReturnCallback(static function () use (&$items) {
                return \next($items);
            });

        $iterator->expects(self::any())
            ->method('key')
            ->willReturnCallback(static function () use (&$items) {
                return \key($items);
            });

        $iterator->expects(self::any())
            ->method('valid')
            ->willReturnCallback(static function () use (&$items) {
                return (bool) \current($items);
            });

        $iterator->expects(self::any())
            ->method('rewind')
            ->willReturnCallback(static function () use (&$items) {
                return (bool) \reset($items);
            });

    }
}

<?php

declare(strict_types=1);

namespace Test\Unit\Infrastructure\Repository;

use DG\BypassFinals;
use PHPUnit\Framework\MockObject\MockObject;
use Whirlwind\Domain\Repository\ResultFactoryInterface;
use Whirlwind\Domain\Repository\ResultInterface;
use Whirlwind\Infrastructure\Hydrator\Hydrator;
use Whirlwind\Infrastructure\Repository\Repository;
use PHPUnit\Framework\TestCase;
use Whirlwind\Infrastructure\Repository\TableGateway\TableGatewayInterface;

class RepositoryTest extends TestCase
{
    private MockObject $tableGateway;
    private MockObject $hydrator;
    private string $modelClass = \stdClass::class;
    private MockObject $resultFactory;

    private Repository $repository;

    protected function setUp(): void
    {
        BypassFinals::enable();
        parent::setUp();

        $this->tableGateway = $this->createMock(TableGatewayInterface::class);
        $this->hydrator = $this->createMock(Hydrator::class);
        $this->resultFactory = $this->createMock(ResultFactoryInterface::class);

        $this->repository = new Repository(
            $this->tableGateway,
            $this->hydrator,
            $this->modelClass,
            $this->resultFactory
        );
    }


    public function testFindAll()
    {
        $items = [
            ['id' => 'test'],
        ];
        $this->tableGateway->expects(self::once())
            ->method('queryAll')
            ->willReturn($items);

        $result = $this->createMock(ResultInterface::class);
        $this->resultFactory->expects(self::once())
            ->method('create')
            ->with(self::identicalTo($items))
            ->willReturn($result);

        $this->mockIterator($result, $items);

        $model = new \stdClass();
        $this->hydrator->expects(self::once())
            ->method('hydrate')
            ->with(self::identicalTo($this->modelClass), self::identicalTo($items[0]))
            ->willReturn($model);

        $result->expects(self::once())
            ->method('offsetSet')
            ->with(self::equalTo(0), self::identicalTo($model));

        $actual = $this->repository->findAll();
        self::assertSame($result, $actual);
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

<?php

declare(strict_types=1);

namespace Test\Unit\Infrastructure\Hydrator;

use DG\BypassFinals;
use Test\Unit\Infrastructure\Hydrator\Stub\StubEntity;
use Whirlwind\Domain\Collection\Collection;
use Whirlwind\Domain\Enum;
use Whirlwind\Infrastructure\Hydrator\Accessor\PropertyAccessor;
use Whirlwind\Infrastructure\Hydrator\Extractor\DateIntervalExtractor;
use Whirlwind\Infrastructure\Hydrator\Extractor\DateTimeExtractor;
use Whirlwind\Infrastructure\Hydrator\Extractor\EnumExtractor;
use Whirlwind\Infrastructure\Hydrator\PresenterHydrator;
use PHPUnit\Framework\TestCase;

class PresenterHydratorTest extends TestCase
{
    private PropertyAccessor $accessor;
    private PresenterHydrator $hydrator;

    protected function setUp(): void
    {
        BypassFinals::enable();
        parent::setUp();

        $this->accessor = new PropertyAccessor();
        $this->hydrator = new PresenterHydrator($this->accessor);
        $this->hydrator->addExtractor(new DateIntervalExtractor());
        $this->hydrator->addExtractor(new DateTimeExtractor());
        $this->hydrator->addExtractor(new EnumExtractor());
    }

    /**
     * @param StubEntity $entity
     * @param array $expected
     * @return void
     * @dataProvider extractDataProvider
     */
    public function testExtract(StubEntity $entity, array $expected)
    {
        $actual = $this->hydrator->extract($entity);

        self::assertSame($expected, $actual);
    }

    public function extractDataProvider(): array
    {
        BypassFinals::enable();
        $enum = $this->createMock(Enum::class);
        $enum->expects(self::any())
            ->method('getValue')
            ->willReturn('enum');

        return [
            [
                'entity' => new StubEntity('test'),
                'expected' => [
                    'value' => 'test',
                ],
            ],
            [
                'entity' => new StubEntity(new StubEntity('test')),
                'expected' => [
                    'value' => [
                        'value' => 'test',
                    ],
                ],
            ],
            [
                'entity' => new StubEntity(new Collection(StubEntity::class, [new StubEntity('test')])),
                'expected' => [
                    'value' => [
                        [
                            'value' => 'test',
                        ],
                    ],
                ],
            ],
            [
                'entity' => new StubEntity(['key' => new StubEntity('test')]),
                'expected' => [
                    'value' => [
                        'key' => [
                            'value' => 'test',
                        ],
                    ],
                ],
            ],
            [
                'entity' => new StubEntity(['test', 'value']),
                'expected' => [
                    'value' => ['test', 'value'],
                ],
            ],
            [
                'entity' => new StubEntity(['test' => ['value']]),
                'expected' => [
                    'value' => ['test' => ['value']],
                ],
            ],
            [
                'entity' => new StubEntity(new \DateInterval('PT1H')),
                'expected' => [
                    'value' => 'PT1H',
                ],
            ],
            [
                'entity' => new StubEntity(new \DateTimeImmutable('2022-05-04 10:27:00')),
                'expected' => [
                    'value' => '2022-05-04 10:27:00',
                ],
            ],
            [
                'entity' => new StubEntity($enum),
                'expected' => [
                    'value' => 'enum',
                ],
            ],
        ];
    }
}

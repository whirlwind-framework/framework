<?php

declare(strict_types=1);

namespace Test\Unit\Infrastructure\Hydrator;

use DG\BypassFinals;
use Test\Unit\Infrastructure\Hydrator\Stub\StubEntity;
use Whirlwind\Domain\Collection\Collection;
use Whirlwind\Infrastructure\Hydrator\Accessor\PropertyAccessor;
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
        ];
    }
}

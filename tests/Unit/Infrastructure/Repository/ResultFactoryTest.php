<?php

declare(strict_types=1);

namespace Test\Unit\Infrastructure\Repository;

use DG\BypassFinals;
use Whirlwind\Infrastructure\Repository\Result;
use Whirlwind\Infrastructure\Repository\ResultFactory;
use PHPUnit\Framework\TestCase;

class ResultFactoryTest extends TestCase
{
    private ResultFactory $factory;

    protected function setUp(): void
    {
        BypassFinals::enable();
        parent::setUp();

        $this->factory = new ResultFactory();
    }


    public function testCreate()
    {
        $items = [
            [
                'id' => 'test',
            ],
        ];

        $actual = $this->factory->create($items);
        self::assertInstanceOf(Result::class, $actual);
    }
}

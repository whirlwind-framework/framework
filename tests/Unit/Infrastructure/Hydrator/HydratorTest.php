<?php

declare(strict_types=1);

namespace Test\Unit\Infrastructure\Hydrator;

use PHPUnit\Framework\TestCase;
use Test\Unit\Fixture\Entity\DummyEntity;
use Test\Unit\Fixture\Entity\DummyEntityWithVo;
use Test\Unit\Fixture\Entity\VoFixture;
use Whirlwind\Infrastructure\Hydrator\Hydrator;
use Whirlwind\Infrastructure\Hydrator\Accessor\PropertyAccessor;
use Whirlwind\Infrastructure\Hydrator\Strategy\ObjectStrategy;

class HydratorTest extends TestCase
{
    protected $hydrator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hydrator = new Hydrator(new PropertyAccessor());
    }

    public function testHydrate()
    {
        $val = 'test value';
        $entity = $this->hydrator->hydrate(DummyEntity::class, ['property' => $val]);
        $this->assertInstanceOf(DummyEntity::class, $entity);
        $this->assertEquals($val, $entity->getProperty());
    }

    public function testExtract()
    {
        $val = 'test value';
        $entity = new DummyEntity();
        $entity->setProperty($val);
        $data = $this->hydrator->extract($entity);
        $this->assertEquals(['property' => $val], $data);
    }

    public function testObjectStrategy()
    {
        $val = 'test value';
        $voValue = 'test vo value';
        $this->hydrator->addStrategy(
            'vo',
            new ObjectStrategy(new Hydrator(new PropertyAccessor()), VoFixture::class)
        );
        $entity = $this->hydrator->hydrate(
            DummyEntityWithVo::class,
            [
                'property' => $val,
                'vo' => ['voVal' => $voValue]
            ]
        );
        $this->assertInstanceOf(VoFixture::class, $entity->getVo());
        $this->assertEquals($voValue, $entity->getVo()->getVoVal());
    }
}

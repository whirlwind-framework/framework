<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Factory;

use Whirlwind\Domain\Factory\UidFactory;
use PHPUnit\Framework\TestCase;

class UidFactoryTest extends TestCase
{
    /** @var  \Whirlwind\Domain\Factory\UidFactory */
    protected $factory;

    public function setUp(): void
    {
        \DG\BypassFinals::enable();
        $this->factory = new UidFactory();
    }

    public function testCreateUnique()
    {
        $uid1 = $this->factory->create();
        $uid2 = $this->factory->create();
        $this->assertNotEquals($uid1, $uid2);
    }
}

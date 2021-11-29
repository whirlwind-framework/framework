<?php

declare(strict_types=1);

namespace Test\Unit\App;

use DG\BypassFinals;
use League\Container\Container;
use PHPUnit\Framework\TestCase;
use Whirlwind\App\Application\Adapter\LeagueApplicationFactoryAdapter;
use Whirlwind\App\Application\Application;

class LeagueApplicationFactoryAdapterTest extends TestCase
{
    protected function setUp(): void
    {
        BypassFinals::enable();
        parent::setUp();
    }

    public function testCreate(): void
    {
        $container = new Container();
        $app = LeagueApplicationFactoryAdapter::create($container);
        $this->assertInstanceOf(Application::class, $app);
    }

}

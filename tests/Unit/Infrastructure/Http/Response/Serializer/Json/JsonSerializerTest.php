<?php

declare(strict_types=1);

namespace Test\Unit\Infrastructure\Http\Response\Serializer\Json;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Test\Unit\Infrastructure\Http\Response\Serializer\Json\Stub\UserResourceStub;
use Test\Unit\Infrastructure\Http\Response\Serializer\Json\Stub\UserStub;
use Whirlwind\Infrastructure\Http\Response\Serializer\Json\JsonSerializer;
use Whirlwind\Infrastructure\Hydrator\Accessor\PropertyAccessor;
use Whirlwind\Infrastructure\Hydrator\Hydrator;

class JsonSerializerTest extends TestCase
{
    protected $container;

    protected $serializer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get', 'has'])
            ->getMock();
        $this->serializer = new JsonSerializer(
            $this->container,
            [
                UserStub::class => UserResourceStub::class
            ]
        );
    }

    public function testSerialize()
    {
        $id = 'hj32j3423j4h2j342';
        $userName = 'uname';
        $password = 'passwd123';
        $user = new UserStub($id, $userName, $password);
        $this->container
            ->expects($this->any())
            ->method('get')
            ->with($this->equalTo(UserResourceStub::class))
            ->will($this->returnValue(new UserResourceStub(new Hydrator(new PropertyAccessor()))));
        $result = $this->serializer->serialize($user);
        $expected = \json_encode(['id' => $id, 'userName' => $userName]);
        $this->assertEquals($expected, $result);
    }
}

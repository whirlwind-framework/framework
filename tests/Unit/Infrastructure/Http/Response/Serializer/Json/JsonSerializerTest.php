<?php

declare(strict_types=1);

namespace Test\Unit\Infrastructure\Http\Response\Serializer\Json;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
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
        $stream = $this->getMockBuilder(StreamInterface::class)
            ->onlyMethods(['write', '__toString', 'close', 'detach', 'getSize', 'tell', 'eof', 'isSeekable', 'seek', 'rewind', 'isWritable', 'isReadable', 'read', 'getContents', 'getMetadata'])
            ->getMock();
        $expected = \json_encode(['id' => $id, 'userName' => $userName]);
        $stream
            ->expects($this->once())
            ->method('write')
            ->with($this->equalTo($expected));
        $response = $this->getMockBuilder(ResponseInterface::class)
            ->onlyMethods(['getBody', 'getStatusCode', 'withStatus', 'getReasonPhrase', 'getProtocolVersion', 'withProtocolVersion', 'getHeaders', 'hasHeader', 'getHeader', 'getHeaderLine', 'withHeader', 'withAddedHeader', 'withoutHeader', 'withBody'])
            ->getMock();
        $response
            ->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue($stream));
        $response
            ->expects($this->once())
            ->method('withHeader')
            ->with($this->equalTo('Content-Type'), $this->equalTo('application/json'))
            ->will($this->returnValue(clone $response));
        $result = $this->serializer->serialize(
            $this->getMockBuilder(ServerRequestInterface::class)->getMock(),
            $response,
            $user
        );
    }
}

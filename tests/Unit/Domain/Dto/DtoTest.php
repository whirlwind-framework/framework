<?php

declare(strict_types=1);

namespace Test\Unit\Domain\Dto;

use PHPUnit\Framework\TestCase;
use Whirlwind\Domain\Dto\Dto;

class DtoTest extends TestCase
{
    protected function getDto($data): Dto
    {
        return new class($data) extends Dto {
            protected $first;
            protected $second;

            public function getFirst(): int
            {
                return (int)$this->first;
            }

            public function getSecond(): string
            {
                return (string)$this->second;
            }
        };
    }

    public function testEnsureDataSctructure()
    {
        $data = [
            'first' => 1,
            'second' => 2
        ];
        $dto = $this->getDto($data);
        $this->assertSame(['first' => 1, 'second' => '2'], $dto->toArray());
        $this->assertNotSame($data, $dto->toArray());
        $data = [
            'first' => '1.0'
        ];
        $dto = $this->getDto($data);
        $this->assertSame(['first' => 1, 'second' => ''], $dto->toArray());
    }
}

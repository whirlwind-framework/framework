<?php declare(strict_types=1);

namespace Whirlwind\Infrastructure\Http\Request;

use Psr\Http\Message\ServerRequestInterface;
use Whirlwind\Domain\Dto\DtoInterface;

abstract class RequestDtoFactory
{
    protected $dtoClass;

    public function __construct(string $dtoClass)
    {
        if (!\is_subclass_of($dtoClass, DtoInterface::class)) {
            throw new \InvalidArgumentException("Class $dtoClass should implement DtoInterface");
        }
        $this->dtoClass = $dtoClass;
    }

    abstract public function extract(ServerRequestInterface $request): array;

    public function create(ServerRequestInterface $request): DtoInterface
    {
        return new $this->dtoClass($this->extract($request));
    }
}

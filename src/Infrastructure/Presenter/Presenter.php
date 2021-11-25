<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Presenter;

use Whirlwind\Domain\Dto\DtoInterface;
use Whirlwind\Domain\Presenter\PresenterInterface;
use Whirlwind\Infrastructure\Hydrator\Hydrator;

class Presenter implements PresenterInterface
{
    protected $hydrator;

    protected $dtoName;

    public function __construct(Hydrator $hydrator, string $dtoName)
    {
        $this->hydrator = $hydrator;
        $this->dtoName = $dtoName;
    }

    public function decorate(object $entity): DtoInterface
    {
        return new $this->dtoName($this->hydrator->extract($entity));
    }
}

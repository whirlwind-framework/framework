<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Presenter;

use Whirlwind\Domain\Dto\DtoInterface;
use Whirlwind\Domain\Presenter\PresenterInterface;
use Whirlwind\Infrastructure\Hydrator\PresenterHydrator;

class Presenter implements PresenterInterface
{
    protected PresenterHydrator $hydrator;

    protected string $dtoName;

    public function __construct(PresenterHydrator $hydrator, string $dtoName)
    {
        $this->hydrator = $hydrator;
        $this->dtoName = $dtoName;
    }

    public function decorate(object $entity): DtoInterface
    {
        return new $this->dtoName($this->hydrator->extract($entity));
    }
}

<?php declare(strict_types=1);

namespace Whirlwind\Domain\Presenter;

use Whirlwind\Domain\Dto\DtoInterface;

interface PresenterInterface
{
    public function decorate(object $entity): DtoInterface;
}

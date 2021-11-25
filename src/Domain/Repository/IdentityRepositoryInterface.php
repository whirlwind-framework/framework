<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Repository;

use Whirlwind\Domain\Entity\IdentityInterface;

interface IdentityRepositoryInterface extends RepositoryInterface
{
    public function persist(IdentityInterface $entity): void;
}

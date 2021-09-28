<?php declare(strict_types=1);

namespace Whirlwind\Infrastructure\Repository;

use Whirlwind\Domain\Entity\IdentityInterface;
use Whirlwind\Domain\Repository\IdentityRepositoryInterface;

class IdentityRepository extends Repository implements IdentityRepositoryInterface
{
    public function persist(IdentityInterface $entity): void
    {
        if (empty($entity->getId())) {
            $this->insert($entity);
        } else {
            $this->update($entity);
        }
    }
}

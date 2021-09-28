<?php declare(strict_types=1);

namespace Whirlwind\Domain\Collection;

use Whirlwind\Domain\Entity\IdentityInterface;

interface IdentityCollectionInterface extends CollectionInterface
{
    public function remove(IdentityInterface $item): void;
}

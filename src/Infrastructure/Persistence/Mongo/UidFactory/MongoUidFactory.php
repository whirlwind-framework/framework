<?php declare(strict_types=1);

namespace Whirlwind\Infrastructure\Persistence\Mongo\UidFactory;

use Whirlwind\Domain\Factory\UidFactoryInterface;
use MongoDB\BSON\ObjectId;

class MongoUidFactory implements UidFactoryInterface
{
    public function create(string $prefix = ''): string
    {
        $uid = new ObjectId();
        return (string)$uid;
    }
}

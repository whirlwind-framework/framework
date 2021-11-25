<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Hydrator;

use Whirlwind\Infrastructure\Hydrator\Hydrator;

class MongoHydrator extends Hydrator
{
    public function hydrate($target, array $data): object
    {
        if (isset($data['_id'])) {
            $data['id'] = (string)$data['_id'];
            unset($data['_id']);
        }
        return parent::hydrate($target, $data);
    }

    public function extract(object $object, array $fields = []): array
    {
        $result = parent::extract($object, $fields);
        if (isset($result['id'])) {
            $result['_id'] = $result['id'];
            unset($result['id']);
        }
        return $result;
    }
}

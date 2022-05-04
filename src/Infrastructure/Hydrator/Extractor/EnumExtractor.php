<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Hydrator\Extractor;

use Whirlwind\Domain\Enum;

class EnumExtractor implements ExtractorInterface
{
    public function extract(object $object)
    {
        return $object->getValue();
    }

    public function isExtractable(object $object): bool
    {
        return $object instanceof Enum;
    }
}

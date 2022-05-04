<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Hydrator\Extractor;

class DateTimeExtractor implements ExtractorInterface
{
    /**
     * @param object $object
     * @return string
     */
    public function extract(object $object): string
    {
        return $object->format('Y-m-d H:i:s');
    }

    public function isExtractable(object $object): bool
    {
        return $object instanceof \DateTimeInterface;
    }
}

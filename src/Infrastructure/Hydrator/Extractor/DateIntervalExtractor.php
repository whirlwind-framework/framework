<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Hydrator\Extractor;

class DateIntervalExtractor implements ExtractorInterface
{
    /**
     * @param object $object
     * @return string
     */
    public function extract(object $object): string
    {
        $value = $object->format('P%yY%mM%dDT%hH%iM%sS');
        $value = \str_replace(['M0S', 'H0M', 'DT0H', 'M0D', 'Y0M', 'P0Y'], ['M', 'H', 'DT', 'M', 'Y', 'P'], $value);
        if ('T' === \substr($value, -1)) {
            $value = \substr($value, 0, -1);
        }
        return $value;
    }

    public function isExtractable(object $object): bool
    {
        return $object instanceof \DateInterval;
    }
}

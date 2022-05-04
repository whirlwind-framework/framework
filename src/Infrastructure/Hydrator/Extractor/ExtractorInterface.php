<?php

declare(strict_types=1);

namespace Whirlwind\Infrastructure\Hydrator\Extractor;

interface ExtractorInterface
{
    public function extract(object $object);
    public function isExtractable(object $object): bool;
}

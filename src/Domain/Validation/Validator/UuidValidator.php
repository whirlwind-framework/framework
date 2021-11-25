<?php

declare(strict_types=1);

namespace Whirlwind\Domain\Validation\Validator;

class UuidValidator extends AbstractValidator
{
    public const VALID_PATTERN = '\A[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}\z';
    public const NIL = '00000000-0000-0000-0000-000000000000';

    /**
     * @var string
     */
    protected $message = 'Value has not valid UUID';

    /**
     * @param $value
     * @param array $context
     * @return bool
     */
    public function validate($value, array $context = []): bool
    {
        if ($this->skipOnEmpty && ($value === null || \trim($value) === '')) {
            return true;
        }

        $uuid = \str_replace(['urn:', 'uuid:', 'URN:', 'UUID:', '{', '}'], '', $value);

        return $uuid === self::NIL || \preg_match('/' . self::VALID_PATTERN . '/Dms', $uuid);
    }
}

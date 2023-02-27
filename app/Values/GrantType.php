<?php

namespace App\Values;

use App\Exceptions\RequiredValueException;

/**
 * [値オブジェクト]
 * 承諾種別
 */
class GrantType
{
    private const ITEM_NAME = '承諾種別';

    private const DEFAULT_VALUE = 'authorization_code';

    private readonly string|null $value;

    public function __construct(
        string|null $value = self::DEFAULT_VALUE,
    ) {
        $this->value = $this->validate($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function validate(string|null $value): string
    {
        if (empty($value)) {
            throw new RequiredValueException();
        }
        return $value;
    }
}

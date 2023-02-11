<?php

namespace App\Values;

use App\Exceptions\RequiredValueException;

/**
 * [値オブジェクト]
 * 認証済みコード：authorize requestのresponseで返却される値
 */
class Code
{
    private const ITEM_NAME = '認証済みコード';

    private readonly string|null $value;

    public function __construct(
        string|null $value,
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

<?php

namespace App\Values;

use App\Exceptions\RequiredValueException;

/**
 * [値オブジェクト]
 * レスポンスタイプ
 * Spotify　for Developersにて取得
 */
class ResponseType
{
    private const ITEM_NAME = 'レスポンスタイプ';

    private const DEFAULT_VALUE = 'code';

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

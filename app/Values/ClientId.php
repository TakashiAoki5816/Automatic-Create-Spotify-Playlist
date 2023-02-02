<?php

namespace App\Values;

use App\Exceptions\RequiredValueException;

/**
 * [値オブジェクト]
 * クライアントID
 * Spotify　for Developersにて取得
 */
class ClientId
{
    private const ITEM_NAME = 'クライアントID';

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

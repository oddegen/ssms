<?php

namespace App\Enums;

use BackedEnum;

/** @mixin BackedEnum */
trait HasEnumValues
{
    /** @return array<string> */
    public static function values(): array
    {
        return array_map(fn ($value) => $value->value, static::cases());
    }

    public function getLabel(): string
    {
        return __($this->value);
    }
}

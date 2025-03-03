<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Gender: string implements HasLabel
{
    use HasEnumValues;
    case male = 'Male';
    case female = 'Female';
}

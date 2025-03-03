<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Role: string implements HasLabel
{
    use HasEnumValues;
    case Admin = 'Admin';
    case Teacher = 'Teacher';
    case Student = 'Student';
}

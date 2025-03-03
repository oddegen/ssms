<?php

namespace App\Filament\Extensions\Infolists\Components;

use Closure;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

class UserGroup
{
    /** @phpstan-ignore-next-line */
    public static function make(array|Closure $schema = []): Group
    {
        if ($schema instanceof Closure) {
            $schema = $schema();
        }

        $schema = array_merge([
            Section::make()
                ->schema([
                    ImageEntry::make('user.image')
                        ->circular()
                        ->label('User Image'),
                    TextEntry::make('user.fullname')
                        ->label('Full Name'),
                    TextEntry::make('user.email')
                        ->label('Email'),
                ]),
            Section::make()
                ->schema([
                    TextEntry::make('user.gender')
                        ->label('Gender'),
                    TextEntry::make('user.address')
                        ->label('Address'),
                    TextEntry::make('user.description')
                        ->label('Description'),
                ]),
        ], $schema);

        return Group::make()
            ->schema($schema);
    }
}

<?php

namespace App\Filament\Extensions\Infolists\Components;

use App\Models\Student;
use Closure;
use Filament\AvatarProviders\Contracts\AvatarProvider;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Model;

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
                        ->label('User Image')
                        ->circular()
                        ->defaultImageUrl(function (Model $record) {
                            $resolver = app(AvatarProvider::class);

                            /** @phpstan-ignore-next-line */
                            return $resolver->get($record->user);
                        }),
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
                        /** @phpstan-ignore-next-line */
                        ->visible(fn (Model $record) => ! is_null($record->user?->address))
                        ->label('Address'),
                    TextEntry::make('user.description')
                        /** @phpstan-ignore-next-line */
                        ->visible(fn (Model $record) => ! is_null($record->user?->description))
                        ->label('Description'),
                ]),
        ], $schema);

        return Group::make()
            ->schema($schema);
    }
}

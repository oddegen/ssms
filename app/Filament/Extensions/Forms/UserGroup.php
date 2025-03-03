<?php

namespace App\Filament\Extensions\Forms;

use App\Enums\Gender;
use Closure;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Str;

class UserGroup
{
    /** @phpstan-ignore-next-line  */
    public static function make(array|Closure $schema = []): Group
    {
        if ($schema instanceof Closure) {
            $schema = $schema();
        }
        $schema = array_merge([
            Section::make()
                ->schema([
                    FileUpload::make('image')
                        ->label('User Image')
                        ->image()
                        ->avatar()
                        ->imageEditor()
                        ->circleCropper()
                        ->directory('users'),
                    TextInput::make('fullname')
                        ->label('Full Name')
                        ->placeholder('Full Name')
                        ->required()
                        ->minLength(3),
                    TextInput::make('email')
                        ->email()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                ]),
            Section::make()
                ->schema([
                    Select::make('gender')
                        ->options(Gender::class)
                        ->default(Gender::female)
                        ->enum(Gender::class),
                    Textarea::make('address')
                        ->autosize(),
                    MarkdownEditor::make('description')
                        ->fileAttachmentsDirectory('users'),
                ]),
        ], $schema);

        return Group::make()
            ->relationship('user')
            ->mutateRelationshipDataBeforeCreateUsing(function (array $data) {
                $data['password'] = bcrypt($password = Str::password(16));

                return $data;
            })
            ->schema($schema);
    }
}

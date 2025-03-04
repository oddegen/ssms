<?php

namespace App\Filament\Resources;

use App\Filament\Extensions\Forms\UserGroup;
use App\Filament\Extensions\Infolists\Components\UserGroup as UserGroupInfolist;
use App\Filament\Resources\TeacherResource\Pages;
use App\Filament\Resources\TeacherResource\RelationManagers\GradesRelationManager;
use App\Filament\Resources\TeacherResource\RelationManagers\SubjectsRelationManager;
use App\Models\Teacher;
use Filament\AvatarProviders\Contracts\AvatarProvider;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontFamily;
use Filament\Tables;
use Filament\Tables\Table;
use Livewire\Component;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $activeNavigationIcon = 'heroicon-s-academic-cap';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        UserGroup::make()
                            ->columnSpan(2),
                        Section::make()
                            ->schema([
                                TextInput::make('employee_number')
                                    ->label('Employee Number')
                                    ->disabled()
                                    ->dehydrated()
                                    ->afterStateHydrated(function (Set $set, Component $livewire) {
                                        if ($livewire instanceof Pages\CreateTeacher) {
                                            $set('employee_number', strtoupper(uniqid('EMP')));
                                        }
                                    })
                                    ->required()
                                    ->minLength(3)
                                    ->suffixAction(
                                        Action::make('generateID')
                                            ->label('Generate ID')
                                            ->hidden(function (Component $livewire) {
                                                return $livewire instanceof Pages\ViewTeacher;
                                            })
                                            ->icon('heroicon-o-arrow-path')
                                            ->action(fn (Set $set, $state) => $set('employee_number', strtoupper(uniqid('EMP')))),
                                    ),
                            ])
                            ->columnSpan(1),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_number')
                    ->label('ID')
                    ->fontFamily(FontFamily::Mono),
                Tables\Columns\ImageColumn::make('user.image')
                    ->circular()
                    ->defaultImageUrl(function (Teacher $record) {
                        $resolver = app(AvatarProvider::class);

                        return $resolver->get($record->user);
                    }),
                Tables\Columns\TextColumn::make('user.fullname')
                    ->label('Full Name'),
                Tables\Columns\TextColumn::make('user.email')->label('Email'),
                Tables\Columns\TextColumn::make('user.address')
                    ->label('Address')
                    ->lineClamp(2),
                Tables\Columns\TextColumn::make('admin.fullname'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Group::make()
                    ->schema([
                        TextEntry::make('employee_number')
                            ->label('Employee Number')
                            ->fontFamily(FontFamily::Mono)
                            ->copyable()
                            ->copyMessage('Employee Number copied to clipboard.'),
                        UserGroupInfolist::make()
                            ->columnSpan(2),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            SubjectsRelationManager::class,
            GradesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeachers::route('/'),
            'create' => Pages\CreateTeacher::route('/create'),
            'view' => Pages\ViewTeacher::route('/{record}'),
            'edit' => Pages\EditTeacher::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('User Management');
    }
}

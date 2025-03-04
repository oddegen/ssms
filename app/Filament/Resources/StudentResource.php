<?php

namespace App\Filament\Resources;

use App\Filament\Extensions\Forms\UserGroup;
use App\Filament\Extensions\Infolists\Components\UserGroup as UserGroupInfolist;
use App\Filament\Resources\StudentResource\Pages;
use App\Models\Grade;
use App\Models\Student;
use Carbon\Carbon;
use Filament\AvatarProviders\Contracts\AvatarProvider;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontFamily;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $activeNavigationIcon = 'heroicon-s-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        UserGroup::make()
                            ->columnSpan(2),
                        Section::make()
                            ->schema([
                                TextInput::make('student_id')
                                    ->label('Student ID')
                                    ->disabled()
                                    ->dehydrated()
                                    ->afterStateHydrated(function (Set $set, Component $livewire) {
                                        if ($livewire instanceof Pages\CreateStudent) {
                                            $set('student_id', strtoupper(uniqid('STU')));
                                        }
                                    })
                                    ->required()
                                    ->minLength(3)
                                    ->suffixAction(
                                        Action::make('generateID')
                                            ->label('Generate ID')
                                            ->hidden(function (Component $livewire) {
                                                return $livewire instanceof Pages\ViewStudent;
                                            })
                                            ->icon('heroicon-o-arrow-path')
                                            ->action(fn (Set $set, $state) => $set('student_id', strtoupper(uniqid('STU')))),
                                    ),
                                Select::make('grade_id')
                                    ->label('Grade')
                                    ->options(fn () => Grade::pluck('name', 'id')->toArray())
                                    ->required(),
                                DatePicker::make('enrollment_date')
                                    ->label('Enrollment Date')
                                    ->format('d/m/Y')
                                    ->dehydrateStateUsing(function ($state) {
                                        return Carbon::parse($state);
                                    })
                                    ->required()
                                    ->default(now())
                                    ->minDate(now()->subYears(15))
                                    ->maxDate(now()),
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
                Tables\Columns\TextColumn::make('student_id')
                    ->label('ID')
                    ->fontFamily(FontFamily::Mono),
                Tables\Columns\TextColumn::make('grade.name'),
                Tables\Columns\ImageColumn::make('user.image')
                    ->circular()
                    ->defaultImageUrl(function (Student $record) {
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
                \Filament\Infolists\Components\Group::make()
                    ->schema([
                        \Filament\Infolists\Components\Group::make([
                            TextEntry::make('student_id')
                                ->label('Student ID')
                                ->fontFamily(FontFamily::Mono)
                                ->copyable()
                                ->copyMessage('Student ID copied to clipboard.'),
                            TextEntry::make('grade.name'),
                        ]),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'view' => Pages\ViewStudent::route('/{record}'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }

    /** @return Builder<Student> */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getNavigationGroup(): ?string
    {
        return __('User Management');
    }
}

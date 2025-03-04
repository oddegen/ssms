<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\ScoreResource\Pages;
use App\Models\Score;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Livewire\Component;

class ScoreResource extends Resource
{
    protected static ?string $model = Score::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    protected static ?string $activeNavigationIcon = 'heroicon-s-table-cells';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label('Student')
                            ->options(
                                Student::with('user')
                                    ->whereIn('grade_id', auth()->user()->teacher->grades->pluck('id'))
                                    ->get()
                                    ->mapWithKeys(fn (Student $student) => [$student->id => $student->user->fullname])
                            )
                            ->required()
                            ->disabledOn('edit')
                            ->live(debounce: 300),
                        Forms\Components\Select::make('subject_id')
                            ->label('Subject')
                            ->options(function (Forms\Get $get) {
                                /** @var Student|null $student */
                                $student = Student::find($get('student_id'));

                                if (! is_null($student)) {
                                    return $student->grade->subjects()->where('teacher_id', auth()->user()->teacher->id)
                                        ->get(['subjects.id', 'name'])
                                        ->mapWithKeys(fn ($subject) => [$subject->id => $subject->name]);
                                }

                            })
                            ->disabled(function (Forms\Get $get, Component $livewire) {
                                if (! $livewire instanceof Pages\CreateScore || is_null($get('student_id'))) {
                                    return true;
                                }
                            })
                            ->required(),
                        Forms\Components\TextInput::make('score')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.user.fullname')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('score')
                    ->numeric()
                    ->sortable(),
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListScores::route('/'),
            'create' => Pages\CreateScore::route('/create'),
            'edit' => Pages\EditScore::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources\TeacherResource\RelationManagers;

use App\Models\Grade;
use App\Models\Subject;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\FontFamily;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SubjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'subjects';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->modifyQueryUsing(fn (Builder $query) => $query->select(DB::raw('distinct subjects.*')))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->url(route('filament.admin.resources.subjects.index')),
                Tables\Columns\TextColumn::make('code')
                    ->fontFamily(FontFamily::Mono),
                Tables\Columns\TextColumn::make('grades.name')
                    ->listWithLineBreaks(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Assign Subjects')
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->options(Subject::query()->pluck('name', 'id')->toArray())
                            ->live(debounce: 500),
                        Select::make('grade_id')
                            ->label('Grade')
                            ->options(function (Get $get) {
                                /** @var Grade|null $subject */
                                $subject = Grade::find($get('recordId'));

                                if (! is_null($subject)) {
                                    return Grade::query()
                                        ->whereDoesntHave('subjects', function ($query) use ($subject) {
                                            $query->where('subject_id', $subject->getKey());
                                        })
                                        ->get()
                                        ->mapWithKeys(fn ($grade) => [$grade->getKey() => $grade->name]);
                                }
                            })
                            ->disabled(fn (Get $get) => is_null($get('recordId')))
                            ->required(),
                    ])
                    ->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->label('Unassign'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}

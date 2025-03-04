<?php

namespace App\Filament\Resources\TeacherResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class GradesRelationManager extends RelationManager
{
    protected static string $relationship = 'grades';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->modifyQueryUsing(fn (Builder $query) => $query->select(DB::raw('distinct grades.*')))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->url(route('filament.admin.resources.grades.index')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
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

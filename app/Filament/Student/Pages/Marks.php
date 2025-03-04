<?php

namespace App\Filament\Student\Pages;

use App\Models\Score;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class Marks extends Page implements HasTable
{
    use HasPageShield, InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $activeNavigationIcon = 'heroicon-s-pencil-square';

    protected static string $view = 'filament.student.pages.marks';

    public function table(Table $table): Table
    {
        return $table
            ->query(Score::query()->where('student_id', auth()->user()->student->id))
            ->columns([
                TextColumn::make('subject.name')
                    ->label('Subject'),
                TextColumn::make('score'),
            ]);
    }
}

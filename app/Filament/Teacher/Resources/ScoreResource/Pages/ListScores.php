<?php

namespace App\Filament\Teacher\Resources\ScoreResource\Pages;

use App\Filament\Teacher\Resources\ScoreResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListScores extends ListRecords
{
    protected static string $resource = ScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

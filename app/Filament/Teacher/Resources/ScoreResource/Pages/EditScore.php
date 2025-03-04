<?php

namespace App\Filament\Teacher\Resources\ScoreResource\Pages;

use App\Filament\Teacher\Resources\ScoreResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScore extends EditRecord
{
    protected static string $resource = ScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

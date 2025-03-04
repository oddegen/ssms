<?php

namespace App\Filament\Teacher\Resources\ScoreResource\Pages;

use App\Filament\Teacher\Resources\ScoreResource;
use Filament\Resources\Pages\CreateRecord;

class CreateScore extends CreateRecord
{
    protected static string $resource = ScoreResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['teacher_id'] = auth()->user()->teacher->id;

        return $data;
    }
}

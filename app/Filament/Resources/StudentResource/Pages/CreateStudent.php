<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Enums\Role;
use App\Filament\Resources\StudentResource;
use App\Models\Student;
use Filament\Resources\Pages\CreateRecord;

/** @property Student|null $record */
class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['admin_id'] = auth()->id();

        return $data;
    }

    public function afterCreate(): void
    {
        $this->record?->user->assignRole(Role::Student->value);
    }
}

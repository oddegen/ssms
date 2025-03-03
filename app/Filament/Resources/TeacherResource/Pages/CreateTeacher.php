<?php

namespace App\Filament\Resources\TeacherResource\Pages;

use App\Enums\Role;
use App\Filament\Resources\TeacherResource;
use App\Models\Teacher;
use Filament\Resources\Pages\CreateRecord;

/** @property Teacher|null $record */
class CreateTeacher extends CreateRecord
{
    protected static string $resource = TeacherResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['admin_id'] = auth()->id();

        return $data;
    }

    public function afterCreate(): void
    {
        $this->record?->user->assignRole(Role::Teacher->value);
    }
}

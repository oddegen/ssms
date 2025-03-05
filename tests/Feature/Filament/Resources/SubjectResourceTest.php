<?php

use App\Enums\Role;
use App\Filament\Resources\SubjectResource;
use App\Filament\Resources\SubjectResource\Pages\ManageSubjects;
use App\Models\Subject;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;

use function Pest\Laravel\get;

beforeEach(function () {
    Filament::setCurrentPanel(
        Filament::getPanel('admin'),
    );
});

it('is not accessible by unauthenticated user', function () {
    get(SubjectResource::getUrl())
        ->assertRedirectToRoute('filament.admin.auth.login');
});

it('is accessible', function () {
    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->assignRole(Role::Admin->value);

    Livewire::actingAs($admin)
        ->test(ManageSubjects::class)
        ->assertSuccessful();
});

it('can list subjects', function () {
    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->assignRole(Role::Admin->value);

    $grades = Subject::factory(5)->create();

    Livewire::actingAs($admin)
        ->test(ManageSubjects::class)
        ->assertCanSeeTableRecords($grades)
        ->assertCountTableRecords($grades->count());
});

it('can create a subject', function () {
    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->assignRole(Role::Admin->value);

    $subject = Subject::factory()->make();

    Livewire::actingAs($admin)
        ->test(ManageSubjects::class)
        ->callAction(CreateAction::class, [
            'name' => $subject->name,
            'code' => $subject->code,
        ])
        ->assertHasNoActionErrors()
        ->assertSuccessful();

    expect(Subject::where([
        'name' => $subject->name,
        'code' => $subject->code,
    ])->exists())->toBeTrue();
});

it('can update a subject', function () {
    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->assignRole(Role::Admin->value);

    $subject = Subject::factory()->create();

    $newSubject = Subject::factory()->make();

    Livewire::actingAs($admin)
        ->test(ManageSubjects::class)
        ->callTableAction(EditAction::class, $subject, [
            'name' => $newSubject->name,
            'code' => $newSubject->code,
        ])
        ->assertHasNoTableActionErrors()
        ->assertSuccessful();

    expect(Subject::where([
        'name' => $newSubject->name,
        'code' => $newSubject->code,
    ])->exists())->toBeTrue();
});

it('can delete a subject', function () {
    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->assignRole(Role::Admin->value);

    $subject = Subject::factory()->create();

    Livewire::actingAs($admin)
        ->test(ManageSubjects::class)
        ->callTableAction(DeleteAction::class, $subject)
        ->assertSuccessful();

    expect(Subject::where('id', $subject->id)->exists())->toBeFalse();
});

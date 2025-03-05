<?php

use App\Enums\Role;
use App\Filament\Resources\GradeResource;
use App\Filament\Resources\GradeResource\Pages\ManageGrades;
use App\Models\Grade;
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
    get(GradeResource::getUrl())
        ->assertRedirectToRoute('filament.admin.auth.login');
});

it('is accessible', function () {
    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->assignRole(Role::Admin->value);

    Livewire::actingAs($admin)
        ->test(ManageGrades::class)
        ->assertSuccessful();
});

it('can list grades', function () {
    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->assignRole(Role::Admin->value);

    $grades = Grade::factory(5)->create();

    Livewire::actingAs($admin)
        ->test(ManageGrades::class)
        ->assertCanSeeTableRecords($grades)
        ->assertCountTableRecords($grades->count());
});

it('can create a grade', function () {
    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->assignRole(Role::Admin->value);

    $grade = Grade::factory()->make();

    Livewire::actingAs($admin)
        ->test(ManageGrades::class)
        ->callAction(CreateAction::class, [
            'name' => $grade->name,
        ])
        ->assertHasNoActionErrors()
        ->assertSuccessful();

    expect(Grade::where('name', $grade->name)->exists())->toBeTrue();
});

it('can update a grade', function () {
    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->assignRole(Role::Admin->value);

    $grade = Grade::factory()->create();

    $newGrade = Grade::factory()->make();

    Livewire::actingAs($admin)
        ->test(ManageGrades::class)
        ->callTableAction(EditAction::class, $grade, [
            'name' => $newGrade->name,
        ])
        ->assertHasNoTableActionErrors()
        ->assertSuccessful();

    expect(Grade::where('name', $newGrade->name)->exists())->toBeTrue();
});

it('can delete a grade', function () {
    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->assignRole(Role::Admin->value);

    $grade = Grade::factory()->create();

    Livewire::actingAs($admin)
        ->test(ManageGrades::class)
        ->callTableAction(DeleteAction::class, $grade)
        ->assertSuccessful();

    expect(Grade::where('id', $grade->id)->exists())->toBeFalse();
});

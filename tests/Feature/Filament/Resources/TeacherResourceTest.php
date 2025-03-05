<?php

use App\Enums\Role;
use App\Filament\Resources\TeacherResource;
use App\Filament\Resources\TeacherResource\Pages\CreateTeacher;
use App\Filament\Resources\TeacherResource\Pages\EditTeacher;
use App\Filament\Resources\TeacherResource\Pages\ListTeachers;
use App\Models\Teacher;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\get;

beforeEach(function () {
    Filament::setCurrentPanel(
        Filament::getPanel('admin'),
    );
});

it('is not accessible by unauthenticated user', function () {
    get(TeacherResource::getUrl())
        ->assertRedirectToRoute('filament.admin.auth.login');
});

it('is accessible', function () {
    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->assignRole(Role::Admin->value);

    Livewire::actingAs($admin)
        ->test(ListTeachers::class)
        ->assertSuccessful();
});

it('can list teachers', function () {
    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->assignRole(Role::Admin->value);

    $teachers = Teacher::factory(5)->create([
        'admin_id' => $admin->id,
    ]);

    Livewire::actingAs($admin)
        ->test(ListTeachers::class)
        ->assertCanSeeTableRecords($teachers)
        ->assertCountTableRecords($teachers->count());
});

it('can create a teacher', function () {
    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->assignRole(Role::Admin->value);

    $teacher = Teacher::factory()->make([
        'user_id' => null,
    ])->setRelation('user', User::factory()->make());

    FileUpload::configureUsing(function (FileUpload $upload) {
        $upload->preserveFilenames();
    });

    Storage::fake('local');
    $image = UploadedFile::fake()->image('avatar.jpg');

    Livewire::actingAs($admin)
        ->test(CreateTeacher::class)
        ->fillForm([
            'user.image' => $image,
            'user.fullname' => $teacher->user->fullname,
            'user.email' => $teacher->user->email,
            'user.gender' => $teacher->user->gender,
            'user.address' => $teacher->user->address,
            'user.description' => $teacher->user->description,
            'employee_number' => $teacher->employee_number,
        ])
        ->call('create')
        ->assertHasNoActionErrors()
        ->assertSuccessful();

    Storage::assertExists('users/'.$image->name);

    expect(Teacher::where([
        'employee_number' => $teacher->employee_number,
        'admin_id' => $admin->id,
    ])->exists())->toBeTrue()->and(User::where([
        'image' => 'users/'.$image->name,
        'fullname' => $teacher->user->fullname,
        'email' => $teacher->user->email,
        'gender' => $teacher->user->gender,
        'address' => $teacher->user->address,
        'description' => $teacher->user->description,
    ])->exists())->toBeTrue();
});

it('can update a teacher', function () {
    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->assignRole(Role::Admin->value);

    $teacher = Teacher::factory()->create([
        'admin_id' => $admin->id,
    ]);

    Livewire::actingAs($admin)
        ->test(EditTeacher::class, [
            'record' => $teacher->getRouteKey(),
        ])
        ->call('save')
        ->fillForm([
            'user.fullname' => 'John Doe',
            'user.email' => 'john@email.com',
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertSuccessful();

    expect(Teacher::where([
        'id' => $teacher->id,
        'user_id' => $teacher->user_id,
        'employee_number' => $teacher->employee_number,
        'admin_id' => $admin->id,
    ])->exists())->toBeTrue()->and(User::where([
        'id' => $teacher->user_id,
        'fullname' => 'John Doe',
        'email' => 'john@email.com',
    ])->exists())->toBeTrue();

});

it('can delete a teacher', function () {
    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->assignRole(Role::Admin->value);

    $teacher = Teacher::factory()->create([
        'admin_id' => $admin->id,
    ]);

    Livewire::actingAs($admin)
        ->test(EditTeacher::class, [
            'record' => $teacher->getRouteKey(),
        ])
        ->callAction(DeleteAction::class)
        ->assertHasNoActionErrors()
        ->assertSuccessful();

    expect(Teacher::where('id', $teacher->id)->exists())->toBeFalse();
});

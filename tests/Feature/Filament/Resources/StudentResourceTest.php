<?php

use App\Enums\Role;
use App\Filament\Resources\StudentResource;
use App\Filament\Resources\StudentResource\Pages\CreateStudent;
use App\Filament\Resources\StudentResource\Pages\EditStudent;
use App\Filament\Resources\StudentResource\Pages\ListStudents;
use App\Models\Student;
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
    get(StudentResource::getUrl())
        ->assertRedirectToRoute('filament.admin.auth.login');
});

it('is accessible', function () {
    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->assignRole(Role::Admin->value);

    Livewire::actingAs($admin)
        ->test(ListStudents::class)
        ->assertSuccessful();
});

it('can list students', function () {
    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->assignRole(Role::Admin->value);

    $students = Student::factory(5)->create([
        'admin_id' => $admin->id,
    ]);

    Livewire::actingAs($admin)
        ->test(ListStudents::class)
        ->assertCanSeeTableRecords($students)
        ->assertCountTableRecords($students->count());
});

it('can create a student', function () {
    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->assignRole(Role::Admin->value);

    $student = Student::factory()->make([
        'user_id' => null,
        'enrollment_date' => now(),
    ])->setRelation('user', User::factory()->make());

    FileUpload::configureUsing(function (FileUpload $upload) {
        $upload->preserveFilenames();
    });

    Storage::fake('local');
    $image = UploadedFile::fake()->image('avatar.jpg');

    Livewire::actingAs($admin)
        ->test(CreateStudent::class)
        ->fillForm([
            'user.image' => $image,
            'user.fullname' => $student->user->fullname,
            'user.email' => $student->user->email,
            'user.gender' => $student->user->gender,
            'user.address' => $student->user->address,
            'user.description' => $student->user->description,
            'student_id' => $student->student_id,
            'grade_id' => $student->grade_id,
            'enrollment_date' => $student->enrollment_date,
        ])
        ->call('create')
        ->assertHasNoActionErrors()
        ->assertSuccessful();

    Storage::assertExists('users/'.$image->name);

    expect(Student::where([
        'student_id' => $student->student_id,
        'grade_id' => $student->grade_id,
        'enrollment_date' => $student->enrollment_date,
        'admin_id' => $admin->id,
    ])->exists())->toBeTrue()->and(User::where([
        'image' => 'users/'.$image->name,
        'fullname' => $student->user->fullname,
        'email' => $student->user->email,
        'gender' => $student->user->gender,
        'address' => $student->user->address,
        'description' => $student->user->description,
    ])->exists())->toBeTrue();
});

it('can update a student', function () {
    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->assignRole(Role::Admin->value);

    $student = Student::factory()->create([
        'admin_id' => $admin->id,
        'enrollment_date' => now(),
    ]);

    Livewire::actingAs($admin)
        ->test(EditStudent::class, [
            'record' => $student->getRouteKey(),
        ])
        ->call('save')
        ->fillForm([
            'user.fullname' => 'John Doe',
            'user.email' => 'john@email.com',
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertSuccessful();

    expect(Student::where([
        'id' => $student->id,
        'user_id' => $student->user_id,
        'grade_id' => $student->grade_id,
        'enrollment_date' => $student->enrollment_date,
        'admin_id' => $admin->id,
    ])->exists())->toBeTrue()->and(User::where([
        'id' => $student->user_id,
        'fullname' => 'John Doe',
        'email' => 'john@email.com',
    ])->exists())->toBeTrue();

});

it('can delete a student', function () {
    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->assignRole(Role::Admin->value);

    $student = Student::factory()->create([
        'admin_id' => $admin->id,
    ]);

    Livewire::actingAs($admin)
        ->test(EditStudent::class, [
            'record' => $student->getRouteKey(),
        ])
        ->callAction(DeleteAction::class)
        ->assertHasNoActionErrors()
        ->assertSuccessful();

    expect(Student::where('id', $student->id)->exists())->toBeFalse();
});

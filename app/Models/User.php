<?php

namespace App\Models;

use App\Enums\Gender;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasAvatar, HasName
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'fullname',
        'email',
        'password',
        'gender',
        'image',
        'address',
        'description',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'gender' => Gender::class,
        ];
    }

    /** @return HasOne<Student, $this> */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    /** @return HasOne<Teacher, $this> */
    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    public function getUserName(): string
    {
        return $this->fullname;
    }

    public function getFilamentName(): string
    {
        return $this->fullname;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->exists && $this->image !== null) {
            return Storage::temporaryUrl($this->image, now()->addHour());
        }

        return null;
    }
}

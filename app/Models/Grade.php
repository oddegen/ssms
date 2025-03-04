<?php

namespace App\Models;

use Database\Factories\GradeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grade extends Model
{
    /** @use HasFactory<GradeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected $casts = [
        'score' => 'decimal:2',
    ];

    /** @return HasMany<Student, $this> */
    public function student(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /** @return BelongsToMany<Subject, $this> */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'grade_subject_teacher')->withPivot('teacher_id');
    }

    /** @return BelongsToMany<Teacher, $this> */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'grade_subject_teacher')->withPivot('subject_id');
    }
}

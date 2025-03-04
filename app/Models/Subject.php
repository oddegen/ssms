<?php

namespace App\Models;

use Database\Factories\SubjectFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subject extends Model
{
    /** @use HasFactory<SubjectFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
    ];

    /** @return BelongsToMany<Teacher, $this> */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'grade_subject_teacher')->withPivot('grade_id');
    }

    /** @return BelongsToMany<Grade, $this> */
    public function grades(): BelongsToMany
    {
        return $this->belongsToMany(Grade::class, 'grade_subject_teacher')->withPivot('teacher_id');
    }
}

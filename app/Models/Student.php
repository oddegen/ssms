<?php

namespace App\Models;

use App\Observers\StudentObserver;
use Database\Factories\StudentFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[ObservedBy(StudentObserver::class)]
class Student extends Model
{
    /** @use HasFactory<StudentFactory> */
    use HasFactory;

    protected $fillable = [
        'student_id',
        'enrollment_date',
        'user_id',
        'admin_id',
        'grade_id',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
    ];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<User, $this> */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /** @return BelongsTo<Grade, $this> */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    /** @return HasManyThrough<Subject, Grade, $this> */
    public function subjects(): HasManyThrough
    {
        // FIXME: Implement custom hasManyThroughPivot relationship
        return $this->hasManyThrough(Subject::class, Grade::class);
    }

    /** @return HasMany<Score, $this> */
    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }
}

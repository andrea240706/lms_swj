<?php

namespace App\Models;

use Database\Factories\RombelFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[Fillable([
    'academic_year_id',
    'department_id',
    'name',
    'grade_level',
    'homeroom_teacher_id',
    'room',
    'capacity',
])]
class Rombel extends Model
{
    /** @use HasFactory<RombelFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
        ];
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function homeroomTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'homeroom_teacher_id');
    }

    /**
     * Siswa yang terdaftar di rombel ini (Auto-Inherit basis).
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'rombel_students', 'rombel_id', 'student_id')
            ->withPivot('enrollment_status')
            ->withTimestamps();
    }

    public function teachingAssignments(): HasMany
    {
        return $this->hasMany(TeachingAssignment::class);
    }

    public function schedules(): HasManyThrough
    {
        return $this->hasManyThrough(Schedule::class, TeachingAssignment::class);
    }
}

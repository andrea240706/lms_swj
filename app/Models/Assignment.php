<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'meeting_id',
    'title',
    'instructions_html',
    'attachment_link_url',
    'submission_mode',
    'available_from',
    'available_until',
    'submission_opens_at',
    'submission_due_at',
    'allow_late_submission',
    'allow_revision',
    'is_published',
    'created_by',
    'updated_by',
])]
class Assignment extends Model
{
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'available_from' => 'datetime',
            'available_until' => 'datetime',
            'submission_opens_at' => 'datetime',
            'submission_due_at' => 'datetime',
            'allow_late_submission' => 'boolean',
            'allow_revision' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(AssignmentFile::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Check whether submissions are currently open.
     */
    public function isOpen(): bool
    {
        $now = Carbon::now();

        if ($this->submission_opens_at && $now->lt($this->submission_opens_at)) {
            return false;
        }

        if ($this->submission_due_at && $now->gt($this->submission_due_at) && ! $this->allow_late_submission) {
            return false;
        }

        return true;
    }
}

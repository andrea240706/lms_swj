<?php

namespace App\Models;

use Database\Factories\MaterialFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'mapel_key',
    'meeting_number',
    'title',
    'description',
    'is_published',
    'created_by',
    'updated_by',
])]
class Material extends Model
{
    /** @use HasFactory<MaterialFactory> */
    use HasFactory;

    public function files(): HasMany
    {
        return $this->hasMany(MaterialFile::class)->orderBy('sort_order')->orderBy('id');
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'meeting_number' => 'integer',
            'is_published' => 'boolean',
        ];
    }
}

<?php

namespace App\Models;

use Database\Factories\MaterialFileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'material_id',
    'disk',
    'path',
    'original_name',
    'mime_type',
    'extension',
    'size',
    'checksum',
    'sort_order',
])]
class MaterialFile extends Model
{
    /** @use HasFactory<MaterialFileFactory> */
    use HasFactory;

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'sort_order' => 'integer',
        ];
    }
}

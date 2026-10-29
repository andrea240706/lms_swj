<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class MaterialFileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'original_name' => $this->original_name,
            'extension' => Str::upper($this->extension),
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'human_size' => Number::fileSize($this->size),
            'download_url' => route('materials.files.download', [
                'material' => $this->material_id,
                'file' => $this->id,
            ]),
        ];
    }
}

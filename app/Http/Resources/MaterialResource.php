<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class MaterialResource extends JsonResource
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
            'mapel_key' => $this->mapel_key,
            'meeting_number' => $this->meeting_number,
            'title' => $this->title,
            'description' => $this->description,
            'is_published' => $this->is_published,
            'files' => MaterialFileResource::collection($this->whenLoaded('files')),
            'files_count' => $this->whenLoaded('files', fn ($files): int => $files->count()),
            'total_size' => $this->whenLoaded('files', fn ($files): int => (int) $files->sum('size')),
            'human_total_size' => $this->whenLoaded(
                'files',
                fn ($files): string => Number::fileSize((int) $files->sum('size'))
            ),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

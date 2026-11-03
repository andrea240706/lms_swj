<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
        $now = Carbon::now();
        $isAvailable = $this->available_from === null || $now->gte($this->available_from);

        return [
            'id' => $this->id,
            'meeting_id' => $this->meeting_id,
            'mapel_key' => $this->mapel_key,
            'meeting_number' => $this->meeting_number,
            'title' => $this->title,
            'description' => $this->description,
            'content_html' => $this->content_html,
            'link_url' => $this->link_url,
            'is_published' => $this->is_published,
            'available_from' => $this->available_from?->toIso8601String(),
            'available_until' => $this->available_until?->toIso8601String(),
            'is_available' => $isAvailable,
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

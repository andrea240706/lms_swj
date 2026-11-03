<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeetingVideoResource extends JsonResource
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
            'title' => $this->title,
            'url' => $this->url,
            'provider' => $this->provider,
            'thumbnail_url' => $this->thumbnail_url,
            'available_from' => $this->available_from?->toIso8601String(),
            'available_until' => $this->available_until?->toIso8601String(),
            'is_available' => $isAvailable,
            'is_published' => $this->is_published,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResource extends JsonResource
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
        $isOpen = $this->isOpen();
        $isPastDue = $this->submission_due_at !== null && $now->gt($this->submission_due_at);

        return [
            'id' => $this->id,
            'meeting_id' => $this->meeting_id,
            'title' => $this->title,
            'instructions_html' => $this->instructions_html,
            'attachment_link_url' => $this->attachment_link_url,
            'submission_mode' => $this->submission_mode, // 'file' | 'link' | 'both'
            'available_from' => $this->available_from?->toIso8601String(),
            'available_until' => $this->available_until?->toIso8601String(),
            'submission_opens_at' => $this->submission_opens_at?->toIso8601String(),
            'submission_due_at' => $this->submission_due_at?->toIso8601String(),
            'allow_late_submission' => $this->allow_late_submission,
            'allow_revision' => $this->allow_revision,
            'is_published' => $this->is_published,
            'is_available' => $isAvailable,
            'is_open' => $isOpen,
            'is_past_due' => $isPastDue,
            'files' => $this->whenLoaded('files', fn () => $this->files->map(fn ($f) => [
                'id' => $f->id,
                'original_name' => $f->original_name,
                'extension' => $f->extension,
                'size' => $f->size,
            ])),
            'submissions_count' => $this->whenCounted('submissions'),
            'user_submission' => $this->whenLoaded('submissions', function () use ($request) {
                $sub = $this->submissions->firstWhere('student_id', $request->user()?->id);

                return $sub ? new AssignmentSubmissionResource($sub) : null;
            }),
        ];
    }
}

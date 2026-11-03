<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentSubmissionResource extends JsonResource
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
            'assignment_id' => $this->assignment_id,
            'student_id' => $this->student_id,
            'student' => $this->whenLoaded('student', fn () => [
                'id' => $this->student->id,
                'name' => $this->student->name,
                'username' => $this->student->username,
            ]),
            'link_url' => $this->link_url,
            'note' => $this->note,
            'submitted_at' => $this->submitted_at?->toIso8601String(),
            'is_late' => $this->is_late,
            'score' => $this->score,
            'feedback' => $this->feedback,
            'graded_at' => $this->graded_at?->toIso8601String(),
            'files' => $this->whenLoaded('files', fn () => $this->files->map(fn ($f) => [
                'id' => $f->id,
                'original_name' => $f->original_name,
                'extension' => $f->extension,
                'size' => $f->size,
            ])),
        ];
    }
}

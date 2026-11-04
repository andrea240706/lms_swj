<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentFile;
use App\Models\Meeting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class AssignmentController extends Controller
{
    /**
     * Store or update an assignment for a meeting.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'meeting_id' => ['required', 'integer', 'exists:meetings,id'],
            'title' => ['required', 'string', 'max:180'],
            'instructions_html' => ['nullable', 'string'],
            'attachment_link_url' => ['nullable', 'url', 'max:2048'],
            'submission_mode' => ['required', 'string', 'in:file,link,both'],
            'available_from' => ['nullable', 'date'],
            'available_until' => ['nullable', 'date'],
            'submission_opens_at' => ['nullable', 'date'],
            'submission_due_at' => ['nullable', 'date'],
            'allow_late_submission' => ['boolean'],
            'allow_revision' => ['boolean'],
            'is_published' => ['boolean'],
            'files' => ['nullable', 'array', 'max:5'],
            'files.*' => ['file', 'max:20480'],
        ]);

        $meeting = Meeting::findOrFail($validated['meeting_id']);
        $files = $request->file('files', []);
        unset($validated['files']);

        $validated['allow_late_submission'] = $request->boolean('allow_late_submission', false);
        $validated['allow_revision'] = $request->boolean('allow_revision', false);
        $validated['is_published'] = $request->boolean('is_published', true);
        $validated['created_by'] = $request->user()->id;
        $validated['updated_by'] = $request->user()->id;

        foreach (['available_from', 'available_until', 'submission_opens_at', 'submission_due_at', 'attachment_link_url', 'instructions_html'] as $field) {
            if (isset($validated[$field]) && trim((string) $validated[$field]) === '') {
                $validated[$field] = null;
            }
        }

        $storedFiles = [];

        try {
            DB::transaction(function () use ($validated, $meeting, $files, &$storedFiles): void {
                $assignment = Assignment::updateOrCreate(
                    ['meeting_id' => $meeting->id],
                    $validated
                );

                if (! empty($files)) {
                    $disk = config('materials.disk', 'local');
                    $sortOrder = (int) $assignment->files()->max('sort_order');

                    foreach ($files as $file) {
                        $extension = Str::lower($file->getClientOriginalExtension());
                        $path = $file->store("assignments/{$assignment->id}", $disk);

                        $storedFiles[] = ['disk' => $disk, 'path' => $path];

                        $assignment->files()->create([
                            'disk' => $disk,
                            'path' => $path,
                            'original_name' => $file->getClientOriginalName(),
                            'mime_type' => $file->getMimeType(),
                            'extension' => $extension,
                            'size' => $file->getSize(),
                            'checksum' => hash_file('sha256', $file->getRealPath()),
                            'sort_order' => ++$sortOrder,
                        ]);
                    }
                }
            });
        } catch (Throwable $e) {
            foreach ($storedFiles as $stored) {
                Storage::disk($stored['disk'])->delete($stored['path']);
            }
            throw $e;
        }

        notify()->success()->title('Berhasil')->message('Tugas berhasil disimpan.')->send();

        return back()->with('success', 'Tugas berhasil disimpan.');
    }

    /**
     * Delete an assignment.
     */
    public function destroy(Assignment $assignment): RedirectResponse
    {
        $storedFiles = $assignment->files()->get(['disk', 'path']);

        $assignment->delete();

        foreach ($storedFiles as $file) {
            Storage::disk($file->disk)->delete($file->path);
        }

        notify()->success()->title('Berhasil')->message('Tugas berhasil dihapus.')->send();

        return back()->with('success', 'Tugas berhasil dihapus.');
    }

    /**
     * Download assignment attachment file.
     */
    public function downloadFile(Assignment $assignment, AssignmentFile $file): StreamedResponse
    {
        abort_unless($file->assignment_id === $assignment->id, 404);
        abort_unless(Storage::disk($file->disk)->exists($file->path), 404);

        return Storage::disk($file->disk)->download($file->path, $file->original_name, [
            'Content-Type' => $file->mime_type ?: 'application/octet-stream',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}

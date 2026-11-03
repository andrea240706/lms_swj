<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\SubmissionFile;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class AssignmentSubmissionController extends Controller
{
    /**
     * Submit an assignment response (file and/or link).
     */
    public function store(Request $request, Assignment $assignment): JsonResponse|RedirectResponse
    {
        $now = Carbon::now();

        // Check if submission is open or allowed
        if (! $assignment->isOpen()) {
            $msg = 'Pengumpulan tugas telah ditutup karena melewati tenggat waktu.';
            if ($request->wantsJson()) {
                return response()->json(['message' => $msg], 422);
            }

            return back()->withErrors(['submission' => $msg]);
        }

        $rules = [
            'note' => ['nullable', 'string', 'max:2000'],
            'link_url' => ['nullable', 'url', 'max:2048'],
            'file' => ['nullable', 'file', 'max:51200'], // 50MB
        ];

        if ($assignment->submission_mode === 'file') {
            $rules['file'] = ['required_without:existing', 'file', 'max:51200'];
        } elseif ($assignment->submission_mode === 'link') {
            $rules['link_url'] = ['required', 'url', 'max:2048'];
        } else {
            // mode 'both'
            $rules['link_url'] = ['nullable', 'url', 'max:2048'];
        }

        $validated = $request->validate($rules);

        $hasFile = $request->hasFile('file');
        $hasLink = ! empty($validated['link_url']);

        if (! $hasFile && ! $hasLink && empty($validated['note'])) {
            $msg = 'Harap sertakan berkas tugas atau tautan pengumpulan.';
            if ($request->wantsJson()) {
                return response()->json(['message' => $msg], 422);
            }

            return back()->withErrors(['submission' => $msg]);
        }

        $isLate = $assignment->submission_due_at !== null && $now->gt($assignment->submission_due_at);
        $user = $request->user();

        $storedFile = null;

        try {
            DB::transaction(function () use ($assignment, $user, $validated, $now, $isLate, $hasFile, $request, &$storedFile): void {
                $submission = AssignmentSubmission::updateOrCreate(
                    [
                        'assignment_id' => $assignment->id,
                        'student_id' => $user->id,
                    ],
                    [
                        'link_url' => $validated['link_url'] ?? null,
                        'note' => $validated['note'] ?? null,
                        'submitted_at' => $now,
                        'is_late' => $isLate,
                    ]
                );

                if ($hasFile) {
                    $file = $request->file('file');
                    $disk = config('materials.disk', 'local');
                    $extension = Str::lower($file->getClientOriginalExtension());
                    $path = $file->store("submissions/{$assignment->id}/{$user->id}", $disk);

                    $storedFile = ['disk' => $disk, 'path' => $path];

                    // Remove old submission files if replacing
                    foreach ($submission->files as $oldFile) {
                        Storage::disk($oldFile->disk)->delete($oldFile->path);
                        $oldFile->delete();
                    }

                    $submission->files()->create([
                        'disk' => $disk,
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'extension' => $extension,
                        'size' => $file->getSize(),
                        'checksum' => hash_file('sha256', $file->getRealPath()),
                        'sort_order' => 1,
                    ]);
                }
            });
        } catch (Throwable $e) {
            if ($storedFile) {
                Storage::disk($storedFile['disk'])->delete($storedFile['path']);
            }
            throw $e;
        }

        $successMsg = $isLate ? 'Tugas berhasil dikumpulkan (Status: Terlambat).' : 'Tugas berhasil dikumpulkan tepat waktu.';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'is_late' => $isLate,
            ]);
        }

        notify()->success()->title('Berhasil')->message($successMsg)->send();

        return back()->with('success', $successMsg);
    }

    /**
     * Grade a student submission.
     */
    public function grade(Request $request, AssignmentSubmission $submission): RedirectResponse
    {
        $validated = $request->validate([
            'score' => ['required', 'integer', 'min:0', 'max:100'],
            'feedback' => ['nullable', 'string', 'max:2000'],
        ]);

        $submission->update([
            'score' => $validated['score'],
            'feedback' => $validated['feedback'] ?? null,
            'graded_at' => Carbon::now(),
            'graded_by' => $request->user()->id,
        ]);

        notify()->success()->title('Tersimpan')->message('Nilai dan evaluasi tugas berhasil disimpan.')->send();

        return back()->with('success', 'Nilai tugas berhasil disimpan.');
    }

    /**
     * Download submission file.
     */
    public function downloadFile(Request $request, AssignmentSubmission $submission, SubmissionFile $file): StreamedResponse
    {
        $user = $request->user();

        // Check permission: must be teacher, admin, or the student themselves
        if (! $user->isTeacher() && ! $user->isAdmin() && $submission->student_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        abort_unless($file->assignment_submission_id === $submission->id, 404);
        abort_unless(Storage::disk($file->disk)->exists($file->path), 404);

        return Storage::disk($file->disk)->download($file->path, $file->original_name, [
            'Content-Type' => $file->mime_type ?: 'application/octet-stream',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}

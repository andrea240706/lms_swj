<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaterialFilesRequest;
use App\Http\Requests\StoreMaterialRequest;
use App\Http\Requests\UpdateMaterialRequest;
use App\Models\Material;
use App\Models\MaterialFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class MaterialController extends Controller
{
    /**
     * Store a new material with its files.
     */
    public function store(StoreMaterialRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $files = $data['files'];
        unset($data['files']);

        $data['is_published'] = $data['is_published'] ?? true;
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        $storedFiles = [];

        try {
            DB::transaction(function () use ($data, $files, &$storedFiles): void {
                $material = Material::create($data);

                $this->saveUploadedFiles($material, $files, $storedFiles);
            });
        } catch (Throwable $exception) {
            $this->deleteStoredFiles($storedFiles);

            throw $exception;
        }

        return back()->with('success', 'Materi berhasil ditambahkan.');
    }

    /**
     * Update the material metadata.
     */
    public function update(UpdateMaterialRequest $request, Material $material): RedirectResponse
    {
        $data = $request->validated();
        $data['updated_by'] = $request->user()->id;

        $material->update($data);

        return back()->with('success', 'Materi berhasil diperbarui.');
    }

    /**
     * Delete the material and all of its stored files.
     */
    public function destroy(Material $material): RedirectResponse
    {
        Gate::authorize('delete', $material);

        $storedFiles = $material->files()
            ->get(['disk', 'path'])
            ->map(fn (MaterialFile $file): array => [
                'disk' => $file->disk,
                'path' => $file->path,
            ])
            ->all();

        $material->delete();
        $this->deleteStoredFiles($storedFiles);

        return back()->with('success', 'Materi berhasil dihapus.');
    }

    /**
     * Add more files to an existing material.
     */
    public function storeFiles(StoreMaterialFilesRequest $request, Material $material): RedirectResponse
    {
        $files = (array) $request->file('files', []);
        $storedFiles = [];

        try {
            DB::transaction(function () use ($material, $files, $request, &$storedFiles): void {
                $this->saveUploadedFiles($material, $files, $storedFiles);
                $material->update(['updated_by' => $request->user()->id]);
            });
        } catch (Throwable $exception) {
            $this->deleteStoredFiles($storedFiles);

            throw $exception;
        }

        return back()->with('success', 'File materi berhasil ditambahkan.');
    }

    /**
     * Remove one file from a material.
     */
    public function destroyFile(Request $request, Material $material, MaterialFile $file): RedirectResponse
    {
        Gate::authorize('update', $material);

        abort_unless($file->material_id === $material->id, 404);

        if ($material->files()->count() <= 1) {
            throw ValidationException::withMessages([
                'file' => 'Materi harus memiliki minimal satu file.',
            ]);
        }

        Storage::disk($file->disk)->delete($file->path);
        $file->delete();

        $material->update(['updated_by' => $request->user()->id]);

        return back()->with('success', 'File materi berhasil dihapus.');
    }

    /**
     * Download a material file as an attachment.
     */
    public function download(Material $material, MaterialFile $file): StreamedResponse
    {
        Gate::authorize('view', $material);

        abort_unless($file->material_id === $material->id, 404);
        abort_unless(Storage::disk($file->disk)->exists($file->path), 404);

        return Storage::disk($file->disk)->download($file->path, $file->original_name, [
            'Content-Type' => $file->mime_type ?: 'application/octet-stream',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Store the uploaded files and create their database records.
     *
     * @param  array<int, UploadedFile>  $files
     * @param  array<int, array{disk: string, path: string}>  $storedFiles
     */
    private function saveUploadedFiles(Material $material, array $files, array &$storedFiles): void
    {
        $disk = config('materials.disk');
        $sortOrder = (int) $material->files()->max('sort_order');

        foreach ($files as $file) {
            $extension = Str::lower($file->getClientOriginalExtension());
            $path = $file->store(
                "materials/{$material->mapel_key}/{$material->meeting_number}",
                $disk
            );

            if ($path === false) {
                throw new RuntimeException('File materi gagal disimpan.');
            }

            $storedFiles[] = ['disk' => $disk, 'path' => $path];

            $material->files()->create([
                'disk' => $disk,
                'path' => $path,
                'original_name' => mb_substr($file->getClientOriginalName(), 0, 255),
                'mime_type' => $file->getMimeType() ?: null,
                'extension' => $extension,
                'size' => (int) $file->getSize(),
                'checksum' => hash_file('sha256', $file->getRealPath()) ?: null,
                'sort_order' => ++$sortOrder,
            ]);
        }
    }

    /**
     * Delete stored files after a failed database write or a material deletion.
     *
     * @param  array<int, array{disk: string, path: string}>  $storedFiles
     */
    private function deleteStoredFiles(array $storedFiles): void
    {
        foreach ($storedFiles as $storedFile) {
            Storage::disk($storedFile['disk'])->delete($storedFile['path']);
        }
    }
}

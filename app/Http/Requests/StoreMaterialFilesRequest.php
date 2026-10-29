<?php

namespace App\Http\Requests;

use App\Models\Material;
use App\Rules\AllowedMaterialFile;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Validator;

class StoreMaterialFilesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $material = $this->route('material');

        return $material instanceof Material
            && ($this->user()?->can('update', $material) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'files' => [
                'required',
                'array',
                'min:1',
                'max:'.config('materials.max_files_per_material'),
            ],
            'files.*' => [
                'required',
                'file',
                'max:'.config('materials.max_file_size_kb'),
                new AllowedMaterialFile,
            ],
        ];
    }

    /**
     * Get the "after" validation callbacks.
     *
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->has('files') || $validator->errors()->has('files.*')) {
                    return;
                }

                $material = $this->route('material');

                if (! $material instanceof Material) {
                    return;
                }

                $files = (array) $this->file('files', []);
                $newFilesCount = count($files);
                $existingFilesCount = $material->files()->count();

                if ($existingFilesCount + $newFilesCount > config('materials.max_files_per_material')) {
                    $validator->errors()->add(
                        'files',
                        'Materi ini sudah memiliki '.$existingFilesCount.' file. Maksimal '.config('materials.max_files_per_material').' file per materi.'
                    );

                    return;
                }

                $newTotalSize = collect($files)->sum(
                    fn (UploadedFile $file): int => $file->getSize()
                );
                $existingTotalSize = $material->files()->sum('size');

                if ($existingTotalSize + $newTotalSize > config('materials.max_total_size_kb') * 1024) {
                    $validator->errors()->add(
                        'files',
                        'Total ukuran file materi melebihi batas '.config('materials.max_total_size_kb').' KB.'
                    );
                }
            },
        ];
    }
}

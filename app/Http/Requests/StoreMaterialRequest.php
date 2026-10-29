<?php

namespace App\Http\Requests;

use App\Models\Material;
use App\Rules\AllowedMaterialFile;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Validator;

class StoreMaterialRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Material::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'mapel_key' => ['required', 'string', 'max:50', 'alpha_dash'],
            'meeting_number' => ['required', 'integer', 'min:1', 'max:100'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_published' => ['sometimes', 'boolean'],
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

                $files = (array) $this->file('files', []);
                $totalSize = collect($files)->sum(
                    fn (UploadedFile $file): int => $file->getSize()
                );

                if ($totalSize > config('materials.max_total_size_kb') * 1024) {
                    $validator->errors()->add(
                        'files',
                        'Total ukuran file melebihi batas '.config('materials.max_total_size_kb').' KB.'
                    );
                }
            },
        ];
    }
}

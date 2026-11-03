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
            'meeting_id' => ['nullable', 'integer', 'exists:meetings,id'],
            'mapel_key' => ['required', 'string', 'max:50', 'alpha_dash'],
            'meeting_number' => ['required', 'integer', 'min:1', 'max:100'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'content_html' => ['nullable', 'string'],
            'link_url' => ['nullable', 'url', 'max:2048'],
            'available_from' => ['nullable', 'date'],
            'available_until' => ['nullable', 'date'],
            'is_published' => ['sometimes', 'boolean'],
            'files' => [
                'nullable',
                'array',
                'max:'.config('materials.max_files_per_material', 10),
            ],
            'files.*' => [
                'file',
                'max:'.config('materials.max_file_size_kb', 51200),
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
                $hasFiles = $this->hasFile('files');
                $hasLink = ! empty($this->input('link_url'));
                $hasContent = ! empty(trim(strip_tags((string) $this->input('content_html'))));

                if (! $hasFiles && ! $hasLink && ! $hasContent) {
                    $validator->errors()->add(
                        'materi_source',
                        'Harap unggah minimal 1 berkas, masukkan tautan materi, atau tuliskan materi melalui editor.'
                    );
                }

                if ($validator->errors()->has('files') || $validator->errors()->has('files.*')) {
                    return;
                }

                $files = (array) $this->file('files', []);
                $totalSize = collect($files)->sum(
                    fn (UploadedFile $file): int => $file->getSize()
                );

                if ($totalSize > config('materials.max_total_size_kb', 204800) * 1024) {
                    $validator->errors()->add(
                        'files',
                        'Total ukuran file melebihi batas '.config('materials.max_total_size_kb', 204800).' KB.'
                    );
                }
            },
        ];
    }
}

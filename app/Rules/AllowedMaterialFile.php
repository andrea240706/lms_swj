<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Translation\PotentiallyTranslatedString;
use Symfony\Component\Mime\MimeTypes;

class AllowedMaterialFile implements ValidationRule
{
    /**
     * Blocked MIME types for native executable formats.
     *
     * @var array<int, string>
     */
    private const BLOCKED_MIME_TYPES = [
        'application/x-dosexec',
        'application/x-executable',
        'application/x-msdownload',
        'application/x-msi',
        'application/x-sharedlib',
    ];

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile || ! $value->isValid()) {
            $fail('File materi tidak valid.');

            return;
        }

        $extension = Str::lower($value->getClientOriginalExtension());

        if ($extension === '' || ! in_array($extension, config('materials.allowed_extensions'), true)) {
            $fail('Format file .'.($extension ?: 'tidak dikenal').' tidak diizinkan.');

            return;
        }

        $mimeType = $value->getMimeType();

        if ($mimeType === null) {
            $fail('Tipe file tidak dapat dikenali.');

            return;
        }

        if (in_array($mimeType, self::BLOCKED_MIME_TYPES, true)) {
            $fail('File executable tidak diizinkan.');

            return;
        }

        if (! $this->mimeTypeMatchesExtension($extension, $mimeType)) {
            $fail('Isi file tidak sesuai dengan format .'.$extension.'.');
        }
    }

    private function mimeTypeMatchesExtension(string $extension, string $mimeType): bool
    {
        $knownMimeTypes = MimeTypes::getDefault()->getMimeTypes($extension);

        if (in_array($mimeType, $knownMimeTypes, true)) {
            return true;
        }

        return in_array($extension, config('materials.text_extensions'), true)
            && Str::startsWith($mimeType, 'text/');
    }
}

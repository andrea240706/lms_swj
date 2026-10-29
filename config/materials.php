<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Material Storage
    |--------------------------------------------------------------------------
    |
    | Materials are private learning assets. Keep them on the private local
    | disk and serve them through an authorized download controller.
    |
    */

    'disk' => env('LMS_MATERIALS_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Upload Limits
    |--------------------------------------------------------------------------
    */

    'max_files_per_material' => 10,

    'max_file_size_kb' => 51200,

    'max_total_size_kb' => 204800,

    /*
    |--------------------------------------------------------------------------
    | Allowed Learning Formats
    |--------------------------------------------------------------------------
    |
    | Extensions are checked against the uploaded file name, then the file
    | contents are compared with the known MIME types for that extension.
    | Source code is allowed as a downloadable attachment for programming
    | subjects, but executable and server-side script extensions stay blocked.
    |
    */

    'allowed_extensions' => [
        'pdf',
        'doc',
        'docx',
        'ppt',
        'pptx',
        'xls',
        'xlsx',
        'csv',
        'txt',
        'rtf',
        'odt',
        'ods',
        'odp',
        'md',
        'jpg',
        'jpeg',
        'png',
        'webp',
        'gif',
        'mp3',
        'wav',
        'ogg',
        'mp4',
        'webm',
        'mov',
        'zip',
        'rar',
        '7z',
        'sql',
        'php',
        'js',
        'ts',
        'html',
        'css',
        'json',
        'xml',
        'py',
        'java',
        'kt',
        'dart',
    ],

    'text_extensions' => [
        'txt',
        'csv',
        'rtf',
        'md',
        'sql',
        'php',
        'js',
        'ts',
        'html',
        'css',
        'json',
        'xml',
        'py',
        'java',
        'kt',
        'dart',
    ],

];

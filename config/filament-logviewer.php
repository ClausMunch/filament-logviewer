<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Log Directory Path
    |--------------------------------------------------------------------------
    |
    | The path where your Laravel application stores log files.
    | By default, this points to storage/logs.
    |
    */
    'path' => storage_path('logs'),

    /*
    |--------------------------------------------------------------------------
    | Maximum File Size
    |--------------------------------------------------------------------------
    |
    | Maximum file size (in bytes) to read. Files larger than this will
    | display a warning and may be truncated to prevent memory issues.
    |
    */
    'max_file_size' => 10 * 1024 * 1024, // 10MB

    /*
    |--------------------------------------------------------------------------
    | Entries Per Page
    |--------------------------------------------------------------------------
    |
    | Number of log entries to display per page in the log viewer.
    |
    */
    'per_page' => 50,

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    |
    | Configure how the log viewer appears in the Filament navigation.
    |
    */
    'navigation' => [
        'group' => 'Settings',
        'sort' => 100,
        'icon' => 'heroicon-o-document-text',
    ],

    /*
    |--------------------------------------------------------------------------
    | Date Format
    |--------------------------------------------------------------------------
    |
    | The format used to display dates in the log viewer.
    |
    */
    'date_format' => 'Y-m-d H:i:s',

    /*
    |--------------------------------------------------------------------------
    | Log Levels
    |--------------------------------------------------------------------------
    |
    | Define the log levels and their display colors.
    |
    */
    'levels' => [
        'emergency' => ['label' => 'Emergency', 'color' => 'danger'],
        'alert' => ['label' => 'Alert', 'color' => 'danger'],
        'critical' => ['label' => 'Critical', 'color' => 'danger'],
        'error' => ['label' => 'Error', 'color' => 'danger'],
        'warning' => ['label' => 'Warning', 'color' => 'warning'],
        'notice' => ['label' => 'Notice', 'color' => 'info'],
        'info' => ['label' => 'Info', 'color' => 'success'],
        'debug' => ['label' => 'Debug', 'color' => 'gray'],
    ],
];

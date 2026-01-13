# Filament Logviewer

A beautiful and powerful Filament plugin to view, filter, and manage Laravel log files directly from your admin panel.

## Features

- 📋 View all log files from `storage/logs`
- 🔍 Parse and display log entries with full context
- 🎯 Filter by log level (emergency, alert, critical, error, warning, notice, info, debug)
- 📅 Filter by date/time range
- 🔎 Search across log messages and context
- 🗑️ Delete individual or multiple log files
- 🧹 Empty log files without deleting them
- 🎨 Color-coded log levels for easy identification
- ⚡ Optimized for large log files

## Installation

Install via composer:

```bash
composer require munch/filament-logviewer
```

Publish the config file (optional):

```bash
php artisan vendor:publish --tag="filament-logviewer-config"
```

## Usage

The plugin automatically registers itself with Filament. Simply navigate to the "Logs" section in your admin panel.

## Configuration

You can customize the behavior in `config/filament-logviewer.php`:

```php
return [
    'path' => storage_path('logs'),
    'max_file_size' => 10 * 1024 * 1024, // 10MB
    'per_page' => 50,
];
```

## Requirements

- PHP 8.2+
- Laravel 12.0+
- Filament 4.0+

## License

MIT License

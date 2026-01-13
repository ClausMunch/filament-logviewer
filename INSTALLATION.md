# Installation & Usage Guide

## Quick Start

### 1. Installation

#### Option A: Local Development (Package in Development)

Add this to your Laravel project's `composer.json`:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "../filament-logviewer"
    }
  ]
}
```

Then require the package:

```bash
composer require munch/filament-logviewer:@dev
```

#### Option B: From Packagist (After Publishing)

Once published to Packagist:

```bash
composer require munch/filament-logviewer
```

### 2. Register the Plugin

The plugin auto-registers itself via the service provider. However, if you prefer manual control, you can also register it in your Filament Panel Provider (e.g., `app/Providers/Filament/AdminPanelProvider.php`):

```php
use Munch\FilamentLogviewer\FilamentLogviewerServiceProvider;

public function panel(Panel $panel): Panel
{
    return $panel
        // ... other configuration
        ->plugins([
            FilamentLogviewerServiceProvider::make(),
        ]);
}
```

**Note:** Manual registration is optional - the plugin registers automatically.

### 3. Publish Configuration (Optional)

```bash
php artisan vendor:publish --tag="filament-logviewer-config"
```

## Features

### Log File Management

- **View All Logs**: Browse all log files in `storage/logs` with file size and last modified date
- **Delete Logs**: Remove individual or multiple log files
- **Empty Logs**: Clear log file contents without deleting the file

### Log Viewing & Filtering

- **Detailed View**: Click "View" on any log file to see parsed entries
- **Filter by Level**: Filter logs by severity (emergency, alert, critical, error, warning, notice, info, debug)
- **Date Range Filter**: Filter logs by timestamp range
- **Search**: Global search across log messages and context
- **Auto-refresh**: Log viewer automatically refreshes every 30 seconds

### Log Entry Display

Each log entry shows:
- Timestamp
- Log level (color-coded badge)
- Environment
- Message
- Context and stack traces (expandable)

## Configuration

Edit `config/filament-logviewer.php` to customize:

```php
return [
    // Path to log files
    'path' => storage_path('logs'),
    
    // Maximum file size to read (10MB default)
    'max_file_size' => 10 * 1024 * 1024,
    
    // Entries per page
    'per_page' => 50,
    
    // Navigation settings
    'navigation' => [
        'group' => 'System',
        'sort' => 100,
        'icon' => 'heroicon-o-document-text',
    ],
    
    // Date format
    'date_format' => 'Y-m-d H:i:s',
    
    // Log level colors
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
```

## Performance

- Large files (>10MB) are automatically handled with optimized reading
- Only the most recent entries are loaded for very large files
- Pagination prevents memory issues
- Configurable per-page limits

## Requirements

- PHP 8.2+
- Laravel 12.0+
- Filament 4.0+

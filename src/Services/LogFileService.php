<?php

namespace Munch\FilamentLogviewer\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class LogFileService
{
    protected string $logPath;

    public function __construct()
    {
        $this->logPath = config('filament-logviewer.path', storage_path('logs'));
    }

    /**
     * Get all log files from the logs directory
     */
    public function getLogFiles(): Collection
    {
        if (! File::isDirectory($this->logPath)) {
            return collect();
        }

        $files = File::files($this->logPath);

        return collect($files)
            ->map(function ($file) {
                return [
                    'name' => $file->getFilename(),
                    'path' => $file->getPathname(),
                    'size' => $file->getSize(),
                    'modified' => Carbon::createFromTimestamp($file->getMTime()),
                ];
            })
            ->sortByDesc('modified')
            ->values();
    }

    /**
     * Parse a log file and return structured entries
     */
    public function parseLogFile(string $filename): Collection
    {
        $filePath = $this->logPath . '/' . $filename;

        if (! File::exists($filePath)) {
            return collect();
        }

        $fileSize = File::size($filePath);
        $maxSize = config('filament-logviewer.max_file_size', 10 * 1024 * 1024);

        if ($fileSize > $maxSize) {
            // For large files, read from the end
            return $this->parseLargeLogFile($filePath);
        }

        $content = File::get($filePath);
        return $this->parseLogContent($content);
    }

    /**
     * Parse log content into structured entries
     */
    protected function parseLogContent(string $content): Collection
    {
        $entries = [];
        $lines = explode("\n", $content);
        $currentEntry = null;

        foreach ($lines as $line) {
            // Check if line starts with a log pattern [YYYY-MM-DD HH:MM:SS]
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2}[T\s]\d{2}:\d{2}:\d{2}(?:\.\d{6})?(?:[+-]\d{2}:\d{2})?)\]\s+(\w+)\.(\w+):\s+(.*)/', $line, $matches)) {
                // Save previous entry if exists
                if ($currentEntry) {
                    $entries[] = $currentEntry;
                }

                // Start new entry
                $currentEntry = [
                    'timestamp' => $this->parseTimestamp($matches[1]),
                    'environment' => $matches[2],
                    'level' => strtolower($matches[3]),
                    'message' => $matches[4],
                    'context' => '',
                    'stacktrace' => [],
                ];
            } elseif ($currentEntry && ! empty(trim($line))) {
                // Continuation of current entry (stack trace or context)
                if (str_starts_with($line, '#') || str_starts_with($line, '  ')) {
                    $currentEntry['stacktrace'][] = $line;
                } else {
                    $currentEntry['context'] .= "\n" . $line;
                }
            }
        }

        // Add the last entry
        if ($currentEntry) {
            $entries[] = $currentEntry;
        }

        return collect($entries)->reverse()->values();
    }

    /**
     * Parse large log files by reading from the end
     */
    protected function parseLargeLogFile(string $filePath): Collection
    {
        $handle = fopen($filePath, 'r');
        if (! $handle) {
            return collect();
        }

        $maxLines = config('filament-logviewer.per_page', 50) * 10;
        $lines = [];
        
        // Read last portion of file
        $fileSize = filesize($filePath);
        $chunkSize = min(512 * 1024, $fileSize); // Read last 512KB
        fseek($handle, max(0, $fileSize - $chunkSize));
        
        while (($line = fgets($handle)) !== false) {
            $lines[] = $line;
        }
        
        fclose($handle);

        $content = implode('', array_slice($lines, -$maxLines));
        return $this->parseLogContent($content);
    }

    /**
     * Parse timestamp from various formats
     */
    protected function parseTimestamp(string $timestamp): Carbon
    {
        try {
            return Carbon::parse($timestamp);
        } catch (\Exception $e) {
            return Carbon::now();
        }
    }

    /**
     * Delete a log file
     */
    public function deleteLogFile(string $filename): bool
    {
        $filePath = $this->logPath . '/' . $filename;

        if (File::exists($filePath)) {
            return File::delete($filePath);
        }

        return false;
    }

    /**
     * Empty a log file (truncate content)
     */
    public function emptyLogFile(string $filename): bool
    {
        $filePath = $this->logPath . '/' . $filename;

        if (File::exists($filePath)) {
            return File::put($filePath, '') !== false;
        }

        return false;
    }

    /**
     * Format file size for display
     */
    public static function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Get log level color based on configuration
     */
    public static function getLevelColor(string $level): string
    {
        $levels = config('filament-logviewer.levels', []);
        return $levels[$level]['color'] ?? 'gray';
    }

    /**
     * Get log level label
     */
    public static function getLevelLabel(string $level): string
    {
        $levels = config('filament-logviewer.levels', []);
        return $levels[$level]['label'] ?? ucfirst($level);
    }
}

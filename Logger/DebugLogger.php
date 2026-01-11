<?php
declare(strict_types=1);

namespace App\Logger;

use Throwable;

final class DebugLogger
{
    private array $errors = [];
    private array $warnings = [];

    public function logError(string $location, Throwable $error, mixed $details = null): void
    {
        $entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'location'  => $location,
            'type'      => get_class($error),
            'message'   => $error->getMessage(),
            'details'   => $details,
            'trace'     => $error->getTraceAsString()
        ];

        $this->errors[] = $entry;

        echo "\n❌ ERROR at {$location}\n";
        echo "   {$entry['type']}: {$entry['message']}\n";

        if ($details !== null) {
            echo "   Details: {$details}\n";
        }

        echo "   Trace:\n{$entry['trace']}\n";
    }

    public function logWarning(string $location, string $message, mixed $details = null): void
    {
        $entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'location'  => $location,
            'message'   => $message,
            'details'   => $details
        ];

        $this->warnings[] = $entry;

        echo "\n⚠️ WARNING at {$location}: {$message}\n";
        if ($details !== null) {
            echo "   Details: {$details}\n";
        }
    }

    public function printSummary(): void
    {
        echo "\n" . str_repeat('=', 60) . "\n";
        echo "📊 DEBUG SUMMARY\n";
        echo str_repeat('=', 60) . "\n";
        echo "Errors: " . count($this->errors) . "\n";
        echo "Warnings: " . count($this->warnings) . "\n";
    }
}

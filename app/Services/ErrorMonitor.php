<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Exception;

class ErrorMonitor
{
    public function logError(Exception $exception, array $context = [])
    {
        Log::error('Application Error', [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'context' => $context,
        ]);

        // Here you could add integration with external monitoring services
        // like Sentry, Bugsnag, or New Relic
    }

    public function logWarning(string $message, array $context = [])
    {
        Log::warning($message, $context);
    }

    public function logInfo(string $message, array $context = [])
    {
        Log::info($message, $context);
    }

    public function logDebug(string $message, array $context = [])
    {
        Log::debug($message, $context);
    }
} 
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Queue monitoring
        Queue::before(function (JobProcessing $event) {
            Log::info('Job processing', [
                'job' => $event->job->resolveName(),
                'connection' => $event->connectionName,
                'queue' => $event->job->getQueue(),
            ]);
        });

        Queue::after(function (JobProcessed $event) {
            Log::info('Job processed', [
                'job' => $event->job->resolveName(),
                'connection' => $event->connectionName,
                'queue' => $event->job->getQueue(),
                'time' => $event->job->resolveName() === 'App\Jobs\ProcessPayment' ? $event->job->payload()['time'] : null,
            ]);
        });

        Queue::failing(function (JobFailed $event) {
            Log::error('Job failed', [
                'job' => $event->job->resolveName(),
                'connection' => $event->connectionName,
                'queue' => $event->job->getQueue(),
                'exception' => $event->exception->getMessage(),
                'trace' => $event->exception->getTraceAsString(),
            ]);
        });

        // Error monitoring
        $this->app->singleton('error.monitor', function ($app) {
            return new \App\Services\ErrorMonitor;
        });
    }
}

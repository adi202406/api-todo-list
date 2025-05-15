<?php

namespace App\Providers;

use App\Services\ReminderService;
use App\Services\NotificationService;
use Illuminate\Support\ServiceProvider;

class ReminderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
        {
            $this->app->singleton(ReminderService::class, function ($app) {
                return new ReminderService(
                    $app->make(NotificationService::class)
                );
            });
        }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

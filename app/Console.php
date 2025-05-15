<?php

namespace App;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\App;

class Console extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('reminders:process')->everyMinute();
    }
}
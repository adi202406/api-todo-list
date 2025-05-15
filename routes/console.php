<?php

use App\Services\ReminderService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Command untuk proses reminder
Artisan::command('reminders:process', function (ReminderService $reminderService) {
    $count = $reminderService->processDueReminders();
    $this->info("Processed {$count} reminders.");
})->purpose('Process due reminders and send notifications');
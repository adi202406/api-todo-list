<?php

namespace App\Console\Commands;

use App\Services\ReminderService;
use Illuminate\Console\Command;

class ProcessReminders extends Command
{
    protected $signature = 'reminders:process';
    protected $description = 'Process due reminders and send notifications';

    public function handle(ReminderService $reminderService)
    {
        $count = $reminderService->processDueReminders();
        $this->info("Processed {$count} reminders.");
        
        return 0;
    }
}
<?php

namespace App\Services;

use App\Models\Reminder;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;

class ReminderService
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function processDueReminders()
    {
        $now = Carbon::now();
        $reminders = Reminder::where('remind_at', '<=', $now)
            ->where('is_sent', false)
            ->with(['card', 'users'])
            ->get();

        foreach ($reminders as $reminder) {
            $this->sendReminderNotifications($reminder);
            $reminder->update(['is_sent' => true]);
        }

        return $reminders->count();
    }

    protected function sendReminderNotifications(Reminder $reminder)
    {
        foreach ($reminder->users as $user) {
            if ($reminder->channel === 'in_app') {
                $this->sendPushNotification($user, $reminder);
            } elseif ($reminder->channel === 'email') {
                $this->sendEmailNotification($user, $reminder);
            }
        }
    }

    protected function sendPushNotification(User $user, Reminder $reminder)
    {
        $title = 'Reminder: ' . $reminder->card->title;
        $body = $reminder->card->description ?? 'You have a reminder!';
        
        $data = [
            'type' => 'reminder',
            'reminder_id' => $reminder->id,
            'card_id' => $reminder->card_id,
        ];

        $this->notificationService->sendPushNotification($user, $title, $body, $data);
    }

    protected function sendEmailNotification(User $user, Reminder $reminder)
    {
        // Implement email sending logic here
        // You can use Laravel's Mail facade or a dedicated email service
    }
}
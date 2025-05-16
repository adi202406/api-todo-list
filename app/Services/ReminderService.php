<?php
namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Reminder;
use App\Mail\ReminderEmail;
use Illuminate\Support\Facades\Mail;
use App\Services\NotificationService;

class ReminderService
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function processDueReminders()
    {
        $now       = Carbon::now();
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
        $body  = $reminder->card->description ?? 'You have a reminder!';

        $data = [
            'type'        => 'reminder',
            'reminder_id' => $reminder->id,
            'card_id'     => $reminder->card_id,
        ];

        $this->notificationService->sendPushNotification($user, $title, $body, $data);
    }

    protected function sendEmailNotification(User $user, Reminder $reminder)
    {
        $maxRetries = 3; // Maximum number of retry attempts
        $retryDelay = 5; // Delay between retries in seconds
        $attempt    = 0;
        $success    = false;

        do {
            $attempt++;
            try {
                Mail::to($user->email)
                    ->queue(new ReminderEmail($user, $reminder));

                \Log::info("Reminder email sent to {$user->email} for reminder {$reminder->id} (Attempt: {$attempt})");
                $success = true;

            } catch (\Exception $e) {
                \Log::warning("Failed to send reminder email to {$user->email} (Attempt: {$attempt}): " . $e->getMessage());

                if ($attempt < $maxRetries) {
                    sleep($retryDelay); // Delay before next attempt
                } else {
                    \Log::error("Max retries reached for reminder email to {$user->email}");
                    // You might want to mark this as failed in database or notify admin
                }
            }
        } while (! $success && $attempt < $maxRetries);

        return $success;
    }
}

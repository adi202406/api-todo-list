<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkspaceInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $workspace;
    protected $inviter;
    protected $role;

    public function __construct($workspace, $inviter, $role)
    {
        $this->workspace = $workspace;
        $this->inviter = $inviter;
        $this->role = $role;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Undangan untuk Bergabung ke Workspace')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line($this->inviter->name . ' telah mengundang Anda untuk bergabung ke workspace "' . $this->workspace->title . '".')
            ->line('Peran Anda di workspace: **' . ucfirst($this->role) . '**')
            ->action('Terima Undangan', url('/workspaces/' . $this->workspace->id))
            ->line('Jika Anda tidak mengenal pengirim undangan ini, Anda dapat mengabaikan email ini.');
    }
}

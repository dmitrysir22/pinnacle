<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class NewAgentRegistered extends Notification
{
    use Queueable;

    public function __construct(
        public User $agent
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Agent Registration Pending Approval | Pinnacle International Freight - Agent Portal')
            ->markdown('emails.admin.new-agent', [
                'agent' => $this->agent,
            ]);
    }
}

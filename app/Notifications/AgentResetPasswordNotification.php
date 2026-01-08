<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class AgentResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable)
    {
        $resetUrl = url(route(
            'password.reset',
            [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ],
            false
        ));

        return (new MailMessage)
            ->subject('Reset Your Password – Pinnacle International Freight | Agent Portal')
            ->markdown('emails.agent.reset-password', [
                'url'  => $resetUrl,
                'user' => $notifiable,
            ]);
    }
}

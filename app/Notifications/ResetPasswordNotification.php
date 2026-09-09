<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Recuperación de contraseña — Portal Municipal Chacabuco')
            ->greeting('Hola,')
            ->line('Recibiste este correo porque solicitaste restablecer la contraseña de tu cuenta.')
            ->action('Restablecer contraseña', $url)
            ->line('Este enlace expira en ' . config('auth.passwords.users.expire') . ' minutos.')
            ->line('Si no realizaste esta solicitud, podés ignorar este correo.')
            ->salutation('Portal Municipal de Chacabuco');
    }
}

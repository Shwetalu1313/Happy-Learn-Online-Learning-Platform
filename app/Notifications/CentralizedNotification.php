<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CentralizedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly array $payload,
        private readonly array $channels
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $this->channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject((string) ($this->payload['subject'] ?? 'New Notification'))
            ->greeting((string) ($this->payload['greeting'] ?? ('Hello, ' . ($notifiable->name ?? 'there'))))
            ->line((string) ($this->payload['line'] ?? 'You have a new update.'));

        if (! empty($this->payload['action_url']) && ! empty($this->payload['action_text'])) {
            $mail->action((string) $this->payload['action_text'], (string) $this->payload['action_url']);
        }

        if (! empty($this->payload['end'])) {
            $mail->line((string) $this->payload['end']);
        }

        return $mail;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->payload;
    }
}

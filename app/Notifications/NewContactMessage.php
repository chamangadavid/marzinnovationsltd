<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContactMessage extends Notification
{
    use Queueable;

    public $contactData;

    /**
     * Create a new notification instance.
     */
    public function __construct($contactData)
    {
        $this->contactData = $contactData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Client Inquiry via Mar’z Innovations Website')
            ->greeting('Dear Team,')
            ->line('You have received a new inquiry via the website contact form. Below are the details:')
            ->line('**Full Name**: ' . $this->contactData['fullName'])
            ->line('**Email Address**: ' . $this->contactData['email'])
            ->line('**Subject**: ' . $this->contactData['subject'])
            ->line('**Message**: ' . $this->contactData['message'])
            ->salutation('Kind regards, Mar’z Innovations Ltd');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}

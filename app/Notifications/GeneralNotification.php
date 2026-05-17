<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification
{
    public function __construct(
        public string $title,
        public string $message,
        public string $type = 'info',   // info, success, warning, error
        public ?string $url = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title'   => $this->title,
            'message' => $this->message,
            'type'    => $this->type,
            'url'     => $this->url,
        ];
    }
}
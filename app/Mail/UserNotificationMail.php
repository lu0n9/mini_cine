<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $title,
        public string $contentMessage,
        public ?string $actionText = null,
        public ?string $actionUrl = null,
        public string $type = 'system',
        public ?User $user = null,
    ) {
    }

    public function build()
    {
        return $this
            ->subject($this->title)
            ->view('mails.user-notification')
            ->with([
                'title'          => $this->title,
                'contentMessage' => $this->contentMessage,
                'actionText'     => $this->actionText,
                'actionUrl'      => $this->actionUrl,
                'type'           => $this->type,
                'user'           => $this->user,
            ]);
    }
}


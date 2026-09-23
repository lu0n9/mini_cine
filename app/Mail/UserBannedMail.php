<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserBannedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user
    ) {
    }

    public function build()
    {
        return $this
            ->subject('Tài khoản Mini Cine của bạn đã bị khóa')
            ->view('mails.user-banned');
    }
}
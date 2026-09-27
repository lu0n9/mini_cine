<?php

namespace App\Console\Commands;

use App\Services\PremiumRenewalReminderService;
use Illuminate\Console\Command;

class SendPremiumRenewalReminders extends Command
{
    protected $signature = 'premium:send-renewal-reminders';

    protected $description = 'Send web and email reminders for Premium subscriptions expiring in five days';

    public function handle(PremiumRenewalReminderService $reminders): int
    {
        $count = $reminders->sendDueReminders();
        $this->info("Sent {$count} Premium renewal reminder(s).");

        return self::SUCCESS;
    }
}

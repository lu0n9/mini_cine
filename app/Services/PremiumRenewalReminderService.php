<?php

namespace App\Services;

use App\Mail\UserNotificationMail;
use App\Models\Notification;
use App\Models\PremiumSubscription;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class PremiumRenewalReminderService
{
    public function sendDueReminders(): int
    {
        $expiryDate = now()->addDays(5)->toDateString();
        $sentCount = 0;

        PremiumSubscription::query()
            ->with(['user', 'plan'])
            ->where('status', 'active')
            ->whereDate('ends_at', $expiryDate)
            ->whereNotNull('user_id')
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('premium_subscriptions as later_subscriptions')
                    ->whereColumn('later_subscriptions.user_id', 'premium_subscriptions.user_id')
                    ->where('later_subscriptions.status', 'active')
                    ->where('later_subscriptions.ends_at', '>', now())
                    ->whereColumn('later_subscriptions.ends_at', '>', 'premium_subscriptions.ends_at');
            })
            ->orderBy('id')
            ->chunkById(200, function ($subscriptions) use ($expiryDate, &$sentCount) {
                foreach ($subscriptions as $subscription) {
                    $user = $subscription->user;
                    if (!$user || !$user->is_active) {
                        continue;
                    }

                    $url = route('premium.index', [
                        'renewal_reminder' => $subscription->id,
                        'expires' => $expiryDate,
                    ]);
                    $title = 'Gói Premium sắp hết hạn';
                    if (Notification::query()->where('user_id', $user->id)
                        ->where('title', $title)->where('url', $url)->exists()) {
                        continue;
                    }

                    $planName = $subscription->plan?->name ?? 'Premium';
                    $expiryText = $subscription->ends_at?->format('d/m/Y');
                    $message = "Gói {$planName} của bạn sẽ hết hạn vào {$expiryText}, còn 5 ngày. Hãy gia hạn để tiếp tục sử dụng Premium.";

                    Notification::create([
                        'user_id' => $user->id,
                        'title' => $title,
                        'message' => $message,
                        'type' => 'account',
                        'url' => $url,
                    ]);

                    if ($user->email) {
                        try {
                            Mail::to($user->email)->send(new UserNotificationMail(
                                title: $title,
                                contentMessage: $message,
                                actionText: 'Gia hạn Premium',
                                actionUrl: $url,
                                type: 'account',
                                user: $user,
                            ));
                        } catch (Throwable $exception) {
                            Log::warning('Premium renewal reminder email failed.', [
                                'user_id' => $user->id,
                                'email' => $user->email,
                                'error' => $exception->getMessage(),
                            ]);
                        }
                    }

                    $sentCount++;
                }
            });

        return $sentCount;
    }
}

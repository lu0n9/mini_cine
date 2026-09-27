<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\PremiumPlan;
use App\Models\PremiumShare;
use App\Models\PremiumSubscription;
use App\Models\PremiumTransaction;
use App\Models\PaymentGatewayConfig;
use App\Models\PremiumCoupon;
use App\Models\PremiumPromotion;
use App\Models\User;
use App\Models\UserPremiumCoupon;
use App\Services\PremiumCouponService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class PremiumController extends Controller
{
    public function index()
    {
        $plans = PremiumPlan::where('is_active', true)->orderBy('price')->get();
        $plansByGrade = $plans->groupBy('code');
        $user = auth()->user();
        if ($user) {
            app(PremiumCouponService::class)->grantEligibleCoupons($user);
        }
        $subscription = $user?->activePremiumSubscription();
        $ownedActiveSubscriptions = $user
            ? $this->ownedActiveSubscriptions((int) $user->id)
            : collect();
        $activeOwnedSubscription = $ownedActiveSubscriptions->first();
        $upgradeCredit = $this->remainingPaidValue($ownedActiveSubscriptions);
        $operationsByPlan = [];
        foreach ($plans as $plan) {
            $operationsByPlan[$plan->id] = $this->operationForPlan($plan, $activeOwnedSubscription?->plan);
        }
        $shares = $subscription && $subscription->plan?->code === 'premium_extra'
            ? $subscription->shares()->with('user:id,name,email')->get()
            : collect();
        $availableCoupons = $user
            ? $user->premiumCouponGrants()
                ->with('coupon')
                ->whereNull('redeemed_at')
                ->whereNull('reserved_at')
                ->latest('granted_at')
                ->get()
                ->filter(fn ($grant) => $grant->coupon?->isAvailable())
                ->values()
            : collect();
        $selectedCouponCode = strtoupper((string) request()->query('coupon', ''));
        $selectedPromotionId = (int) request()->query('promotion', 0);
        $activePromotions = PremiumPromotion::with('plans')
            ->where('is_active', true)
            ->where(fn ($query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($query) => $query->whereNull('ends_at')->orWhere('ends_at', '>', now()))
            ->latest()
            ->get()
            ->filter(fn ($promotion) => $promotion->isAvailableNow())
            ->values();
        $promotionsByPlan = [];
        foreach ($plans as $plan) {
            $eligiblePromotions = $activePromotions->filter(function ($promotion) use ($plan, $selectedPromotionId, $operationsByPlan) {
                return $promotion->plans->contains('id', $plan->id)
                    && (!$selectedPromotionId || $promotion->id === $selectedPromotionId)
                    && (($operationsByPlan[$plan->id] ?? 'purchase') !== 'upgrade' || $promotion->applies_to_upgrades);
            });
            $promotionsByPlan[$plan->id] = $eligiblePromotions
                ->sortByDesc(fn ($promotion) => $promotion->discountFor((int) $plan->price))
                ->first();
        }

        return view('client.pages.premium.index', compact(
            'plans',
            'subscription',
            'shares',
            'availableCoupons',
            'selectedCouponCode',
            'promotionsByPlan',
            'selectedPromotionId',
            'plansByGrade',
            'activeOwnedSubscription',
            'upgradeCredit',
            'operationsByPlan'
        ));
    }

    public function checkout(Request $request, PremiumPlan $plan)
    {
        abort_unless($plan->is_active, 404);
        $validated = $request->validate([
            'coupon_code' => ['nullable', 'string', 'max:50'],
            'promotion_id' => ['nullable', 'integer', 'exists:premium_promotions,id'],
        ]);

        $this->releaseExpiredCouponReservations();

        $reference = now()->format('ymdHis') . Str::upper(Str::random(8));
        try {
            [$subscription, $transaction] = DB::transaction(function () use ($request, $plan, $validated, $reference) {
                $activeSubscriptions = $this->ownedActiveSubscriptions((int) $request->user()->id, true);
                $currentSubscription = $activeSubscriptions->first();
                $operation = $this->operationForPlan($plan, $currentSubscription?->plan);
                if ($operation === 'downgrade') {
                    throw ValidationException::withMessages([
                        'plan' => 'Bạn đang dùng hạng cao hơn. Hãy gia hạn hạng hiện tại hoặc nâng cấp lên hạng cao hơn.',
                    ]);
                }

                $availableUpgradeCredit = $operation === 'upgrade'
                    ? $this->remainingPaidValue($activeSubscriptions)
                    : 0;
                $grant = null;
                $coupon = null;
                $promotion = null;
                $couponDiscount = 0;
                $promotionDiscount = 0;

                if (!empty($validated['coupon_code'])) {
                    $code = strtoupper(trim($validated['coupon_code']));
                    $grant = $request->user()->premiumCouponGrants()
                        ->whereNull('redeemed_at')
                        ->whereNull('reserved_at')
                        ->whereHas('coupon', fn ($query) => $query->where('code', $code))
                        ->lockForUpdate()
                        ->first();

                    if (!$grant) {
                        throw ValidationException::withMessages([
                            'coupon_code' => 'Phiếu này không thuộc tài khoản của bạn, đã được sử dụng hoặc đang được thanh toán.',
                        ]);
                    }

                    $coupon = PremiumCoupon::whereKey($grant->premium_coupon_id)->lockForUpdate()->first();
                    if (!$coupon || !$coupon->isAvailable()) {
                        throw ValidationException::withMessages([
                            'coupon_code' => 'Coupon đã hết hạn, bị tắt hoặc hết lượt sử dụng.',
                        ]);
                    }

                    $couponDiscount = $coupon->discountFor($plan->price);
                }

                if (!empty($validated['promotion_id'])) {
                    $promotion = PremiumPromotion::whereKey($validated['promotion_id'])
                        ->with('plans')
                        ->lockForUpdate()
                        ->first();
                    if (!$promotion || !$promotion->isAvailableNow() || !$promotion->plans->contains('id', $plan->id)
                        || ($operation === 'upgrade' && !$promotion->applies_to_upgrades)) {
                        throw ValidationException::withMessages([
                            'promotion_id' => 'Khuyến mại đã kết thúc hoặc không áp dụng cho gói này.',
                        ]);
                    }
                    $promotionDiscount = $promotion->discountFor((int) $plan->price);
                }

                $useCoupon = $coupon !== null;
                $usePromotion = $promotion !== null;
                $discount = $promotionDiscount + $couponDiscount;
                $discountAmount = min((int) $plan->price, $discount);
                $creditAmount = min($availableUpgradeCredit, max(0, (int) $plan->price - $discountAmount));
                $amount = max(0, (int) $plan->price - $discountAmount - $creditAmount);
                $discountParts = [];
                $isFullyDiscounted = $amount === 0;

                if ($usePromotion) {
                    $discountParts[] = 'Khuyến mại · ' . Str::limit($promotion->title, 95);
                }
                if ($useCoupon) {
                    if (!$isFullyDiscounted) {
                        $coupon->increment('reserved_count');
                        $grant->update(['reserved_at' => now()]);
                    }
                    $discountParts[] = 'Coupon ' . $coupon->code . ' · ' . Str::limit($coupon->title, 80);
                }
                $discountDescription = implode(' + ', $discountParts);

                $renewalStart = $activeSubscriptions->max('ends_at');
                $startsAt = $isFullyDiscounted
                    ? ($operation === 'renewal' && $renewalStart?->isFuture() ? $renewalStart->copy() : now())
                    : null;
                if ($isFullyDiscounted && $operation === 'upgrade') {
                    PremiumSubscription::where('user_id', $request->user()->id)
                        ->where('status', 'active')->where('ends_at', '>', now())
                        ->update(['status' => 'cancelled']);
                }
                $subscription = PremiumSubscription::create([
                    'user_id' => $request->user()->id,
                    'premium_plan_id' => $plan->id,
                    'status' => $isFullyDiscounted ? 'active' : 'pending',
                    'starts_at' => $startsAt,
                    'ends_at' => $isFullyDiscounted
                        ? $startsAt->copy()->addMonths($plan->billing_period === 'yearly' ? 12 : 1)
                        : null,
                ]);
                $transaction = PremiumTransaction::create([
                    'subscription_id' => $subscription->id,
                    'premium_coupon_id' => $coupon?->id,
                    'user_premium_coupon_id' => $grant?->id,
                    'premium_promotion_id' => $usePromotion ? $promotion->id : null,
                    'order_reference' => $reference,
                    'amount' => $amount,
                    'base_amount' => $plan->price,
                    'discount_amount' => $discountAmount,
                    'discount_description' => $discountAmount > 0 ? $discountDescription : null,
                    'operation_type' => $operation,
                    'upgrade_credit' => $creditAmount,
                    'status' => $isFullyDiscounted ? 'paid' : 'pending',
                    'provider' => $isFullyDiscounted ? 'discount' : 'vnpay',
                    'paid_at' => $isFullyDiscounted ? now() : null,
                ]);

                if ($useCoupon && $isFullyDiscounted) {
                    $coupon->update(['usage_count' => $coupon->usage_count + 1]);
                    $grant->update(['redeemed_at' => now()]);
                }

                return [$subscription, $transaction];
            });
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors())->withInput();
        }

        if ($transaction->status === 'paid') {
            return redirect()->route('premium.invoice', $transaction)
                ->with('success', 'Ưu đãi đã giảm toàn bộ giá gói. Hóa đơn đã được tạo.');
        }

        $config = $this->vnpayConfig();
        if (empty($config['enabled']) || empty($config['tmn_code']) || empty($config['hash_secret']) || empty($config['url'])) {
            DB::transaction(function () use ($transaction) {
                $locked = PremiumTransaction::whereKey($transaction->id)->lockForUpdate()->first();
                if (!$locked || $locked->status !== 'pending') {
                    return;
                }

                if ($locked->user_premium_coupon_id) {
                    $grant = UserPremiumCoupon::whereKey($locked->user_premium_coupon_id)->lockForUpdate()->first();
                    if ($grant?->reserved_at) {
                        PremiumCoupon::whereKey($grant->premium_coupon_id)->lockForUpdate()->first()?->decrement('reserved_count');
                        $grant->update(['reserved_at' => null]);
                    }
                }

                $locked->update(['status' => 'failed']);
                PremiumSubscription::whereKey($locked->subscription_id)->where('status', 'pending')->update(['status' => 'cancelled']);
            });

            return back()->with('error', 'VNPay chưa được cấu hình hoặc đang tắt. Hãy kiểm tra mục Thanh toán trong Quản lý API.');
        }

        $params = [
            'vnp_Version' => '2.1.0',
            'vnp_Command' => 'pay',
            'vnp_TmnCode' => $config['tmn_code'],
            'vnp_Amount' => $transaction->amount * 100,
            'vnp_CurrCode' => 'VND',
            'vnp_TxnRef' => $reference,
            'vnp_OrderInfo' => 'Thanh toan ' . $plan->name . ' ' . $plan->billing_period,
            'vnp_OrderType' => 'other',
            'vnp_Locale' => 'vn',
            'vnp_ReturnUrl' => route('premium.vnpay.return'),
            'vnp_IpAddr' => $request->ip(),
            'vnp_CreateDate' => now()->format('YmdHis'),
            'vnp_ExpireDate' => now()->addMinutes(15)->format('YmdHis'),
        ];

        ksort($params);
        $query = http_build_query($params);
        $params['vnp_SecureHash'] = hash_hmac('sha512', $query, $config['hash_secret']);

        return redirect()->away(rtrim($config['url'], '?') . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986));
    }

    public function vnpayReturn(Request $request)
    {
        $params = $request->query();
        $receivedHash = (string) ($params['vnp_SecureHash'] ?? '');
        unset($params['vnp_SecureHash'], $params['vnp_SecureHashType']);
        ksort($params);
        $expectedHash = hash_hmac('sha512', http_build_query($params), $this->vnpayConfig()['hash_secret'] ?? '');

        if (!$receivedHash || !hash_equals($expectedHash, $receivedHash)) {
            return redirect()->route('premium.index')->with('error', 'Chữ ký phản hồi VNPay không hợp lệ.');
        }

        $transaction = PremiumTransaction::with('subscription.plan')
            ->where('order_reference', $request->query('vnp_TxnRef'))
            ->first();

        if (!$transaction || (int) $request->query('vnp_Amount') !== $transaction->amount * 100) {
            return redirect()->route('premium.index')->with('error', 'Không tìm thấy giao dịch hoặc số tiền không khớp.');
        }

        if ($request->query('vnp_ResponseCode') === '00' && $request->query('vnp_TransactionStatus') === '00') {
            DB::transaction(function () use ($transaction, $request) {
                $locked = PremiumTransaction::whereKey($transaction->id)->lockForUpdate()->first();
                if ($locked->status === 'paid') return;

                $sub = PremiumSubscription::whereKey($locked->subscription_id)->lockForUpdate()->first();
                $activeSubscriptions = PremiumSubscription::with('plan')
                    ->where('user_id', $sub->user_id)
                    ->where('status', 'active')->where('ends_at', '>', now())
                    ->orderByDesc('ends_at')->lockForUpdate()->get();
                $current = $activeSubscriptions->first();
                $sameTier = $current && $current->plan?->code === $sub->plan?->code;
                $operation = $locked->operation_type ?: ($sameTier ? 'renewal' : 'purchase');

                if ($operation === 'upgrade') {
                    PremiumSubscription::where('user_id', $sub->user_id)
                        ->where('status', 'active')->where('ends_at', '>', now())
                        ->update(['status' => 'cancelled']);
                    $start = now();
                } elseif ($operation === 'renewal' || $sameTier || $current) {
                    $latestEnd = $activeSubscriptions->max('ends_at');
                    $start = $latestEnd && $latestEnd->isFuture() ? $latestEnd->copy() : now();
                } else {
                    $start = now();
                }
                $end = $start->copy()->addMonths($sub->plan->billing_period === 'yearly' ? 12 : 1);

                $sub->update(['status' => 'active', 'starts_at' => $start, 'ends_at' => $end]);
                $locked->update([
                    'status' => 'paid',
                    'provider_transaction_id' => $request->query('vnp_TransactionNo'),
                    'provider_response' => $request->query(),
                    'paid_at' => now(),
                ]);

                if ($locked->user_premium_coupon_id) {
                    $grant = UserPremiumCoupon::whereKey($locked->user_premium_coupon_id)
                        ->lockForUpdate()
                        ->first();

                    if ($grant && $grant->reserved_at && !$grant->redeemed_at) {
                        $coupon = PremiumCoupon::whereKey($grant->premium_coupon_id)
                            ->lockForUpdate()
                            ->first();

                        if ($coupon) {
                            $coupon->update([
                                'reserved_count' => max(0, $coupon->reserved_count - 1),
                                'usage_count' => $coupon->usage_count + 1,
                            ]);
                        }

                        $grant->update([
                            'reserved_at' => null,
                            'redeemed_at' => now(),
                        ]);
                    }
                }
            });

            return redirect()->route('premium.invoice', $transaction)->with('success', 'Thanh toán thành công.');
        }

        DB::transaction(function () use ($transaction, $request) {
            $locked = PremiumTransaction::whereKey($transaction->id)->lockForUpdate()->first();
            if ($locked->status !== 'pending') {
                return;
            }

            if ($locked->user_premium_coupon_id) {
                $grant = UserPremiumCoupon::whereKey($locked->user_premium_coupon_id)
                    ->lockForUpdate()
                    ->first();

                if ($grant && $grant->reserved_at) {
                    $coupon = PremiumCoupon::whereKey($grant->premium_coupon_id)
                        ->lockForUpdate()
                        ->first();

                    if ($coupon) {
                        $coupon->update(['reserved_count' => max(0, $coupon->reserved_count - 1)]);
                    }

                    $grant->update(['reserved_at' => null]);
                }
            }

            $locked->update(['status' => 'failed', 'provider_response' => $request->query()]);
            PremiumSubscription::whereKey($locked->subscription_id)
                ->where('status', 'pending')
                ->update(['status' => 'cancelled']);
        });

        return redirect()->route('premium.index')->with('error', 'Thanh toán chưa thành công. Bạn có thể thử lại.');
    }

    public function addShare(Request $request)
    {
        $data = $request->validate(['email' => ['required', 'email', 'exists:users,email']]);
        $subscription = $request->user()->activePremiumSubscription();

        if (!$subscription || $subscription->plan?->code !== 'premium_extra') {
            return back()->with('error', 'Chỉ chủ tài khoản Premium Extra đang hoạt động mới chia sẻ được.');
        }

        $sharedUser = User::where('email', $data['email'])->firstOrFail();
        if ($sharedUser->id === $request->user()->id) {
            return back()->with('error', 'Bạn không thể chia sẻ gói cho chính mình.');
        }
        if ($subscription->shares()->count() >= $subscription->plan->share_limit) {
            return back()->with('error', 'Premium Extra đã đủ 2 tài khoản được chia sẻ.');
        }
        $existingShare = PremiumShare::where('shared_user_id', $sharedUser->id)->first();
        if ($existingShare && $existingShare->subscription->status === 'active' && $existingShare->subscription->ends_at?->isFuture()) {
            return back()->with('error', 'Tài khoản này đã được chia sẻ Premium từ một người khác.');
        }
        $existingShare?->delete();

        $subscription->shares()->create(['shared_user_id' => $sharedUser->id]);
        return back()->with('success', 'Đã chia sẻ Premium Extra với ' . $sharedUser->email . '.');
    }

    public function removeShare(PremiumShare $share)
    {
        $subscription = auth()->user()->activePremiumSubscription();
        abort_unless($subscription && $share->subscription_id === $subscription->id, 403);
        $share->delete();
        return back()->with('success', 'Đã gỡ tài khoản khỏi nhóm chia sẻ.');
    }

    public function invoice(Request $request, PremiumTransaction $transaction)
    {
        $transaction->load(['subscription.plan', 'subscription.user', 'coupon', 'promotion']);
        abort_unless((int) $transaction->subscription?->user_id === (int) $request->user()->id, 403);
        abort_unless($transaction->status === 'paid', 404);

        return view('client.pages.premium.invoice', compact('transaction'));
    }

    private function vnpayConfig(): array
    {
        $stored = PaymentGatewayConfig::where('provider', 'vnpay')->first();
        if ($stored) {
            return [
                'tmn_code' => $stored->merchant_code,
                'hash_secret' => $stored->secret_key,
                'url' => $stored->payment_url,
                'enabled' => $stored->is_enabled,
            ];
        }

        return [
            'tmn_code' => config('services.vnpay.tmn_code'),
            'hash_secret' => config('services.vnpay.hash_secret'),
            'url' => config('services.vnpay.url'),
            'enabled' => (bool) (config('services.vnpay.tmn_code') && config('services.vnpay.hash_secret')),
        ];
    }

    private function releaseExpiredCouponReservations(): void
    {
        UserPremiumCoupon::query()
            ->whereNotNull('reserved_at')
            ->where('reserved_at', '<=', now()->subMinutes(20))
            ->orderBy('id')
            ->get()
            ->each(function (UserPremiumCoupon $candidate) {
                DB::transaction(function () use ($candidate) {
                    $grant = UserPremiumCoupon::whereKey($candidate->id)->lockForUpdate()->first();
                    if (!$grant || !$grant->reserved_at || $grant->reserved_at->gt(now()->subMinutes(20))) {
                        return;
                    }

                    $coupon = PremiumCoupon::whereKey($grant->premium_coupon_id)->lockForUpdate()->first();
                    if ($coupon) {
                        $coupon->update(['reserved_count' => max(0, $coupon->reserved_count - 1)]);
                    }

                    PremiumTransaction::where('user_premium_coupon_id', $grant->id)
                        ->where('status', 'pending')
                        ->update(['status' => 'failed']);
                    PremiumSubscription::whereIn('id', PremiumTransaction::where('user_premium_coupon_id', $grant->id)->pluck('subscription_id'))
                        ->where('status', 'pending')
                        ->update(['status' => 'cancelled']);

                    $grant->update(['reserved_at' => null]);
                });
            });
    }

    /**
     * Return every active owned subscription, including queued renewals. Queued
     * rows are needed so renewal dates and upgrade credit match the full paid term.
     */
    private function ownedActiveSubscriptions(int $userId, bool $lock = false): Collection
    {
        $query = PremiumSubscription::query()
            ->with([
                'plan',
                'transactions' => fn ($transactions) => $transactions
                    ->where('status', 'paid')
                    ->orderByDesc('paid_at')
                    ->orderByDesc('id'),
            ])
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->orderByDesc('ends_at')
            ->orderByDesc('id');

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->get();
    }

    /**
     * Prorate the net service value of each unexpired term by its remaining
     * seconds. Earlier upgrade credit is part of that value and carries forward.
     */
    private function remainingPaidValue(Collection $subscriptions): int
    {
        $nowTimestamp = now()->getTimestamp();
        $credit = 0;

        foreach ($subscriptions as $subscription) {
            $transaction = $subscription->transactions->first();
            $end = $subscription->ends_at;
            if (!$transaction || !$end || $end->getTimestamp() <= $nowTimestamp) {
                continue;
            }

            $start = $subscription->starts_at;
            if (!$start) {
                $months = $subscription->plan?->billing_period === 'yearly' ? 12 : 1;
                $start = $end->copy()->subMonths($months);
            }

            $startTimestamp = $start->getTimestamp();
            $endTimestamp = $end->getTimestamp();
            $termSeconds = max(1, $endTimestamp - $startTimestamp);
            $remainingSeconds = max(0, $endTimestamp - max($nowTimestamp, $startTimestamp));

            $serviceValue = (int) $transaction->amount + (int) ($transaction->upgrade_credit ?? 0);
            $credit += (int) round($serviceValue * $remainingSeconds / $termSeconds);
        }

        return $credit;
    }

    private function operationForPlan(PremiumPlan $plan, ?PremiumPlan $currentPlan): string
    {
        if (!$currentPlan) {
            return 'purchase';
        }

        if ($plan->code === $currentPlan->code) {
            return 'renewal';
        }

        return $this->gradeRank($plan->code) > $this->gradeRank($currentPlan->code)
            ? 'upgrade'
            : 'downgrade';
    }

    private function gradeRank(string $code): int
    {
        return match ($code) {
            'premium' => 1,
            'premium_extra' => 2,
            default => 0,
        };
    }

}

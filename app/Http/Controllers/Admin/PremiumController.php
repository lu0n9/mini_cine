<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PremiumCoupon;
use App\Models\Notification;
use App\Models\PremiumPlan;
use App\Models\PremiumPromotion;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PremiumController extends Controller
{
    public function coupons()
    {
        $coupons = PremiumCoupon::withCount('userGrants')->latest()->paginate(20);

        return view('admin.pages.premium.coupons', compact('coupons'));
    }

    public function storeCoupon(Request $request)
    {
        if ($request->filled('code')) {
            $request->merge(['code' => strtoupper(trim($request->input('code')))]);
        }

        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9_-]+$/', 'unique:premium_coupons,code'],
            'title' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:2000'],
            'discount_type' => ['required', 'in:percentage,fixed'],
            'discount_value' => ['required', 'integer', 'min:1', 'max:100000000'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'available_days' => ['nullable', 'array'],
            'available_days.*' => ['integer', 'between:1,7'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'eligibility_type' => ['required', 'in:all,account_age_days,watch_hours,premium_spend'],
            'eligibility_value' => [
                'nullable',
                'integer',
                'min:1',
                'max:100000000',
                'required_if:eligibility_type,account_age_days',
                'required_if:eligibility_type,watch_hours',
                'required_if:eligibility_type,premium_spend',
            ],
        ], [
            'code.regex' => 'Mã coupon chỉ được chứa chữ, số, dấu gạch ngang và gạch dưới.',
            'code.unique' => 'Mã coupon này đã tồn tại.',
            'ends_at.after' => 'Thời điểm kết thúc phải sau thời điểm bắt đầu.',
        ]);

        if ($data['discount_type'] === 'percentage' && $data['discount_value'] > 100) {
            return back()->withInput()->withErrors([
                'discount_value' => 'Mức giảm theo phần trăm không được vượt quá 100%.',
            ]);
        }

        $data['code'] = strtoupper($data['code']);
        $data['eligibility_value'] = $data['eligibility_value'] ?? 0;
        $data['available_days'] = isset($data['available_days'])
            ? array_values(array_unique(array_map('intval', $data['available_days'])))
            : null;
        $data['is_active'] = $request->boolean('is_active');
        $notifiedCount = DB::transaction(function () use ($data) {
            $coupon = PremiumCoupon::create($data);
            $couponCanBeAnnounced = $coupon->is_active
                && (!$coupon->ends_at || $coupon->ends_at->isFuture())
                && (!$coupon->usage_limit || ($coupon->usage_count + $coupon->reserved_count) < $coupon->usage_limit);

            return $couponCanBeAnnounced
                ? $this->notifyEligibleCouponUsers($coupon)
                : 0;
        });

        return back()->with('success', "Đã tạo coupon và gửi thông báo đến {$notifiedCount} user đủ điều kiện.");
    }

    public function toggleCoupon(PremiumCoupon $coupon)
    {
        $coupon->update(['is_active' => !$coupon->is_active]);

        return back()->with('success', 'Đã cập nhật trạng thái coupon.');
    }

    public function deleteCoupon(PremiumCoupon $coupon)
    {
        $coupon->delete();

        return back()->with('success', 'Đã xóa coupon.');
    }

    public function promotions()
    {
        $promotions = PremiumPromotion::with('plans')->latest()->paginate(20);
        $plans = PremiumPlan::where('is_active', true)->orderBy('price')->get();

        return view('admin.pages.premium.promotions', compact('promotions', 'plans'));
    }

    public function storePromotion(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'message' => ['required', 'string', 'max:2000'],
            'action_url' => ['nullable', 'url:http,https', 'max:255'],
            'discount_type' => ['required', 'in:percentage,fixed'],
            'discount_value' => ['required', 'integer', 'min:1', 'max:100000000'],
            'applies_to_upgrades' => ['nullable', 'boolean'],
            'plan_ids' => ['required', 'array', 'min:1'],
            'plan_ids.*' => ['integer', 'distinct', 'exists:premium_plans,id'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'available_days' => ['nullable', 'array'],
            'available_days.*' => ['integer', 'between:1,7'],
        ], [
            'ends_at.after' => 'Thời điểm kết thúc phải sau thời điểm bắt đầu.',
            'available_days.*.between' => 'Ngày áp dụng không hợp lệ.',
        ]);

        if ($data['discount_type'] === 'percentage' && $data['discount_value'] > 100) {
            return back()->withInput()->withErrors([
                'discount_value' => 'Mức giảm theo phần trăm không được vượt quá 100%.',
            ]);
        }

        $planIds = array_values(array_unique(array_map('intval', $data['plan_ids'])));
        unset($data['plan_ids']);
        $data['available_days'] = isset($data['available_days'])
            ? array_values(array_unique(array_map('intval', $data['available_days'])))
            : null;
        $data['is_active'] = $request->boolean('is_active');
        $data['applies_to_upgrades'] = $request->boolean('applies_to_upgrades');
        DB::transaction(function () use ($data, $planIds) {
            $promotion = PremiumPromotion::create($data);
            $promotion->plans()->sync($planIds);
            if ($promotion->is_active) {
                $this->notifyActiveUsersAboutPromotion($promotion);
            }
        });

        return back()->with('success', 'Đã tạo khuyến mại và gửi thông báo trên web đến các user đang hoạt động.');
    }

    public function togglePromotion(PremiumPromotion $promotion)
    {
        $promotion->update(['is_active' => !$promotion->is_active]);

        return back()->with('success', 'Đã cập nhật trạng thái khuyến mại.');
    }

    public function deletePromotion(PremiumPromotion $promotion)
    {
        $promotion->delete();

        return back()->with('success', 'Đã xóa khuyến mại.');
    }
    public function subscriptions()
    {
        $subscriptions = \App\Models\PremiumSubscription::with(['user:id,name,email', 'plan'])
            ->latest()->paginate(20);
        return view('admin.pages.premium.subscriptions', compact('subscriptions'));
    }
    public function transactions()
    {
        $transactions = \App\Models\PremiumTransaction::with('subscription.user:id,name,email')
            ->latest()->paginate(20);
        $monthlyRevenue = \App\Models\PremiumTransaction::where('status', 'paid')
            ->whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year)->sum('amount');
        $successCount = \App\Models\PremiumTransaction::where('status', 'paid')->count();
        $transactionCount = \App\Models\PremiumTransaction::count();
        $successRate = $transactionCount ? round($successCount * 100 / $transactionCount, 1) : 0;
        return view('admin.pages.premium.transactions', compact('transactions', 'monthlyRevenue', 'successCount', 'transactionCount', 'successRate'));
    }
    public function plans()
    {
        $plansByGrade = PremiumPlan::orderBy('code')->orderBy('price')->get()->groupBy('code');
        return view('admin.pages.premium.plans', compact('plansByGrade'));
    }

    public function storePlan(Request $request, string $code)
    {
        $grade = PremiumPlan::where('code', $code)->firstOrFail();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'billing_period' => ['required', 'in:monthly,yearly'],
            'price' => ['required', 'integer', 'min:1000', 'max:100000000'],
            'share_limit' => ['required', 'integer', 'min:0', 'max:2'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        PremiumPlan::create([
            'code' => $grade->code,
            'name' => $data['name'],
            'billing_period' => $data['billing_period'],
            'price' => $data['price'],
            'share_limit' => $data['share_limit'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Đã thêm gói vào hạng ' . $grade->name . '.');
    }

    public function updatePlan(\Illuminate\Http\Request $request, \App\Models\PremiumPlan $plan)
    {
        $data = $request->validate([
            'price' => ['required', 'integer', 'min:1000', 'max:100000000'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $plan->update(['price' => $data['price'], 'is_active' => $request->boolean('is_active')]);

        return back()->with('success', 'Đã cập nhật gói.');
    }

    private function notifyEligibleCouponUsers(PremiumCoupon $coupon): int
    {
        $eligibleUsers = User::query()
            ->where('role', 'user')
            ->where('is_active', true)
            ->whereNotExists(function ($query) use ($coupon) {
                $query->selectRaw('1')
                    ->from('user_premium_coupons')
                    ->whereColumn('user_premium_coupons.user_id', 'users.id')
                    ->where('user_premium_coupons.premium_coupon_id', $coupon->id);
            });

        match ($coupon->eligibility_type) {
            'account_age_days' => $eligibleUsers->where('created_at', '<=', now()->subDays($coupon->eligibility_value)),
            'watch_hours' => $eligibleUsers->whereIn('id', DB::table('watch_histories')
                ->select('user_id')->groupBy('user_id')
                ->havingRaw('SUM(watch_time) >= ?', [$coupon->eligibility_value * 3600])),
            'premium_spend' => $eligibleUsers->whereIn('id', DB::table('premium_transactions')
                ->join('premium_subscriptions', 'premium_subscriptions.id', '=', 'premium_transactions.subscription_id')
                ->where('premium_transactions.status', 'paid')
                ->select('premium_subscriptions.user_id')->groupBy('premium_subscriptions.user_id')
                ->havingRaw('SUM(premium_transactions.amount) >= ?', [$coupon->eligibility_value])),
            default => $eligibleUsers,
        };

        $notifiedCount = 0;
        $eligibleUsers->select('users.id as id')->orderBy('users.id')->chunkById(500, function ($users) use ($coupon, &$notifiedCount) {
            $now = now();
            $grantRows = [];
            $notificationRows = [];
            $url = route('premium.index', ['coupon' => $coupon->code]);
            $discount = $coupon->discount_type === 'percentage'
                ? $coupon->discount_value . '%'
                : '₫' . number_format($coupon->discount_value, 0, ',', '.');

            foreach ($users as $user) {
                $grantRows[] = [
                    'user_id' => $user->id,
                    'premium_coupon_id' => $coupon->id,
                    'granted_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $notificationRows[] = [
                    'user_id' => $user->id,
                    'title' => 'Bạn có phiếu ưu đãi mới',
                    'message' => "Mã {$coupon->code} giảm {$discount} cho gói Premium. Hãy chọn mã này khi thanh toán.",
                    'type' => 'promotion',
                    'url' => $url,
                    'read_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if ($grantRows) {
                DB::table('user_premium_coupons')->insertOrIgnore($grantRows);
                Notification::insert($notificationRows);
                $notifiedCount += count($notificationRows);
            }
        }, 'users.id', 'id');

        return $notifiedCount;
    }

    private function notifyActiveUsersAboutPromotion(PremiumPromotion $promotion): void
    {
        $url = route('premium.index', ['promotion' => $promotion->id]);
        $discount = $promotion->discount_type === 'percentage'
            ? $promotion->discount_value . '%'
            : '₫' . number_format($promotion->discount_value, 0, ',', '.');
        $planNames = $promotion->plans()->pluck('name')->join(', ');
        $message = "{$promotion->title}: giảm {$discount} cho {$planNames}. {$promotion->message}";

        User::query()->where('role', 'user')->where('is_active', true)
            ->select('users.id as id')->orderBy('users.id')->chunkById(500, function ($users) use ($promotion, $url, $message) {
                $now = now();
                Notification::insert($users->map(fn ($user) => [
                    'user_id' => $user->id,
                    'title' => 'Khuyến mại Premium mới',
                    'message' => $message,
                    'type' => 'promotion',
                    'url' => $url,
                    'read_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all());
            }, 'users.id', 'id');
    }
}

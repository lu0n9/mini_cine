@extends('client.layouts.master')
@section('title', 'Gói thành viên Premium')
@section('content')
<div style="max-width:1100px;margin:24px auto;color:#f8fafc">
    <div style="text-align:center;margin-bottom:30px">
        <p style="color:#a78bfa;font-weight:700;letter-spacing:.12em">MINI CINE PREMIUM</p>
        <h1 style="font-size:clamp(30px,5vw,48px);font-weight:800;margin:8px 0">Chọn cách bạn muốn xem phim</h1>
        <p style="color:#cbd5e1">Miễn phí xem 10% mỗi phim. Premium mở khóa toàn bộ thư viện.</p>
    </div>
    @if(session('success'))<div style="padding:14px;margin:12px 0;background:#064e3b;border-radius:10px">{{ session('success') }}</div>@endif
    @if(session('error'))<div style="padding:14px;margin:12px 0;background:#7f1d1d;border-radius:10px">{{ session('error') }}</div>@endif
    @if($errors->any())<div style="padding:14px;margin:12px 0;background:#7f1d1d;border-radius:10px">{{ $errors->first() }}</div>@endif
    @php
        $ownedGrade = $activeOwnedSubscription?->plan?->code;
        $selectedGrade = $ownedGrade && $plansByGrade->has($ownedGrade) ? $ownedGrade : ($plansByGrade->keys()->first() ?: 'free');
    @endphp
    @auth
        @if($availableCoupons->isNotEmpty())
            <section style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin:0 0 16px;padding:10px 14px;background:#211832;border:1px solid #6d4aa8;border-radius:11px">
                <strong style="color:#ddd6fe;font-size:13px">Phiếu ưu đãi</strong>
                <div style="display:flex;gap:6px;flex-wrap:wrap">
                    @foreach($availableCoupons as $grant)
                        <span style="padding:5px 8px;border-radius:7px;background:#111827;color:#e9d5ff;font-size:11px">{{ $grant->coupon->title }} · <b>{{ $grant->coupon->code }}</b></span>
                    @endforeach
                </div>
                <span style="color:#cbd5e1;font-size:11px">Chọn mã ở gói muốn mua.</span>
            </section>
        @endif
    @endauth
    <nav class="premium-grade-tabs" aria-label="Chọn hạng Premium">
        <button type="button" data-grade-tab="free" aria-pressed="{{ $selectedGrade === 'free' ? 'true' : 'false' }}" class="{{ $selectedGrade === 'free' ? 'is-selected' : '' }}">
            <strong>Miễn phí</strong><small>Xem 10% phim</small>
        </button>
        @foreach($plansByGrade as $code => $gradePlans)
            @php $gradeName = $gradePlans->first()->name; @endphp
            <button type="button" data-grade-tab="{{ $code }}" aria-pressed="{{ $selectedGrade === $code ? 'true' : 'false' }}" class="{{ $selectedGrade === $code ? 'is-selected' : '' }}">
                <strong>{{ $gradeName }}</strong><small>{{ $gradePlans->count() }} gói</small>
            </button>
        @endforeach
    </nav>

    <section data-grade-panel="free" class="premium-free-panel" @if($selectedGrade !== 'free') hidden @endif>
        <h2>Hạng Miễn phí</h2>
        <p>Xem tối đa 10% thời lượng mỗi phim. Quảng cáo có thể xuất hiện.</p>
    </section>

    @foreach($plansByGrade as $code => $gradePlans)
        <section data-grade-panel="{{ $code }}" class="premium-grade-panel" @if($selectedGrade !== $code) hidden @endif>
            <div class="premium-package-grid">
                @foreach($gradePlans as $plan)
                    @php
                        $promotion = $promotionsByPlan[$plan->id] ?? null;
                        $promotionDiscount = $promotion ? $promotion->discountFor((int) $plan->price) : 0;
                        $operation = $operationsByPlan[$plan->id] ?? 'purchase';
                        $selectedCodeForPlan = strtoupper((string) old('coupon_code', $selectedCouponCode));
                        $selectedGrantForPlan = $availableCoupons->first(fn ($grant) => strtoupper((string) $grant->coupon?->code) === $selectedCodeForPlan);
                        $initialCouponDiscount = $selectedGrantForPlan?->coupon?->discountFor((int) $plan->price) ?? 0;
                        $creditForPlan = $operation === 'upgrade' ? min($upgradeCredit, max(0, $plan->price - $promotionDiscount - $initialCouponDiscount)) : 0;
                        $initialPrice = max(0, $plan->price - $promotionDiscount - $initialCouponDiscount - $creditForPlan);
                        $isCurrentPackage = $activeOwnedSubscription?->premium_plan_id === $plan->id;
                        $promotionLabelText = $promotion && $promotionDiscount > 0
                            ? $promotion->title . ' · Giảm ' . ($promotion->discount_type === 'percentage' ? $promotion->discount_value . '%' : '₫' . number_format($promotion->discount_value, 0, ',', '.'))
                            : '';
                    @endphp
                    <article class="premium-package-card {{ $isCurrentPackage ? 'is-current' : '' }}">
                        <h2>{{ $plan->name }} <small>{{ $plan->billing_period === 'monthly' ? 'Tháng' : 'Năm' }}</small></h2>
                        @if($isCurrentPackage)<span class="premium-current-badge">Gói đang dùng</span>@endif
                        <p data-premium-price data-base-price="{{ $plan->price }}" data-promotion-discount="{{ $promotionDiscount }}" data-upgrade-credit="{{ $operation === 'upgrade' ? $upgradeCredit : 0 }}" data-promotion-label="{{ $promotionLabelText }}" class="premium-package-price">
                            <span data-price-current>₫{{ number_format($initialPrice, 0, ',', '.') }}</span>
                            <del data-price-original @if($promotionDiscount + $initialCouponDiscount + $creditForPlan <= 0) hidden @endif>₫{{ number_format($plan->price, 0, ',', '.') }}</del>
                            <small data-price-label @if($promotionDiscount + $initialCouponDiscount + $creditForPlan <= 0) hidden @endif>
                                @if($promotionLabelText){{ $promotionLabelText }}@endif
                                @if($creditForPlan > 0){{ $promotionLabelText ? ' + ' : '' }}Số dư gói cũ −₫{{ number_format($creditForPlan, 0, ',', '.') }}@endif
                            </small>
                        </p>
                        <div class="premium-package-description">Xem toàn bộ phim, không giới hạn.{{ $plan->share_limit ? ' Chia sẻ cho tối đa ' . $plan->share_limit . ' tài khoản.' : '' }}</div>

                        @if($operation === 'downgrade')
                            <p class="premium-plan-locked">Gói hiện tại đang hoạt động. Bạn có thể gia hạn hoặc nâng cấp lên hạng cao hơn.</p>
                            <button class="premium-purchase-button is-disabled" type="button" disabled>Chưa thể hạ hạng</button>
                        @else
                            @auth
                                <form method="POST" action="{{ route('premium.checkout', $plan) }}" class="premium-checkout-form">@csrf
                                    @if($promotion && $promotionDiscount > 0)
                                        <input type="hidden" name="promotion_id" value="{{ $promotion->id }}">
                                    @endif
                                    @if($availableCoupons->isNotEmpty())
                                        <label>Phiếu giảm giá
                                            <select name="coupon_code">
                                                <option value="">Không dùng coupon</option>
                                                @foreach($availableCoupons as $grant)
                                                    <option value="{{ $grant->coupon->code }}" data-discount="{{ $grant->coupon->discountFor((int) $plan->price) }}" data-title="{{ $grant->coupon->title }}" data-discount-label="{{ $grant->coupon->discount_type === 'percentage' ? $grant->coupon->discount_value . '%' : '₫' . number_format($grant->coupon->discount_value, 0, ',', '.') }}" {{ old('coupon_code', $selectedCouponCode) === $grant->coupon->code ? 'selected' : '' }}>
                                                        {{ $grant->coupon->code }} · {{ $grant->coupon->discount_type === 'percentage' ? $grant->coupon->discount_value . '%' : '₫' . number_format($grant->coupon->discount_value, 0, ',', '.') }} · {{ $grant->coupon->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </label>
                                    @endif
                                    <button class="premium-purchase-button" type="submit">
                                        {{ $operation === 'renewal' ? 'Gia hạn gói' : ($operation === 'upgrade' ? 'Nâng cấp gói' : 'Mua gói') }}
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="premium-purchase-button">Đăng nhập để mua</a>
                            @endauth
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
    @endforeach

    @auth
        @if($subscription && $subscription->plan?->code === 'premium_extra' && $subscription->user_id === auth()->id())
            <section style="margin-top:28px;background:#111827;border:1px solid #334155;border-radius:16px;padding:24px">
                <h2 style="font-size:22px;font-weight:800">Chia sẻ Premium Extra</h2>
                <p style="color:#cbd5e1">Đang dùng {{ $shares->count() }}/{{ $subscription->plan->share_limit }} tài khoản chia sẻ. Người nhận cần có tài khoản MINI CINE.</p>
                @if($shares->count() < $subscription->plan->share_limit)
                    <form method="POST" action="{{ route('premium.shares.store') }}" style="display:flex;gap:10px;flex-wrap:wrap;margin:14px 0">@csrf
                        <input type="email" name="email" required placeholder="Email tài khoản nhận chia sẻ" style="flex:1;min-width:220px;padding:11px;border-radius:8px;border:1px solid #475569;background:#0f172a;color:white">
                        <button style="padding:11px 18px;border:0;border-radius:8px;background:#7c3aed;color:white;font-weight:700">Thêm tài khoản</button>
                    </form>
                @endif
                @foreach($shares as $share)
                    <div style="display:flex;justify-content:space-between;align-items:center;border-top:1px solid #334155;padding:12px 0">{{ $share->user->name }} · {{ $share->user->email }}
                        <form method="POST" action="{{ route('premium.shares.destroy', $share) }}">@csrf @method('DELETE')<button style="color:#fca5a5;background:transparent;border:0;cursor:pointer">Gỡ</button></form>
                    </div>
                @endforeach
            </section>
        @elseif($subscription)
            <p style="margin-top:20px;color:#c4b5fd">Gói {{ $subscription->plan->name }} đang hoạt động đến {{ $subscription->ends_at?->format('d/m/Y') }}.</p>
        @endif
    @endauth
</div>
<style>
.premium-grade-tabs{display:flex;gap:9px;flex-wrap:wrap;margin:16px 0}.premium-grade-tabs button{display:grid;gap:4px;min-width:145px;padding:12px 16px;border:1px solid #34303d;border-radius:11px;background:#111318;color:#f8fafc;text-align:left;cursor:pointer}.premium-grade-tabs button small{color:#a1a1aa;font-size:11px}.premium-grade-tabs button.is-selected{border-color:#9b70ff;background:#211832;box-shadow:0 0 0 1px #9b70ff inset}.premium-grade-tabs button:focus-visible,.premium-purchase-button:focus-visible{outline:2px solid #c4b5fd;outline-offset:2px}.premium-free-panel,.premium-grade-panel{margin-top:12px}.premium-free-panel{padding:22px;border:1px solid #34303d;border-radius:14px;background:#111318}.premium-free-panel h2{margin:0 0 7px}.premium-free-panel p{margin:0;color:#cbd5e1}.premium-package-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(245px,1fr));gap:14px}.premium-package-card{position:relative;padding:19px;border:1px solid #34303d;border-radius:14px;background:#111318}.premium-package-card.is-current{border-color:#9b70ff}.premium-package-card h2{display:flex;align-items:baseline;justify-content:space-between;gap:8px;margin:0 0 14px;font-size:19px}.premium-package-card h2 small{color:#a1a1aa;font-size:12px;font-weight:500}.premium-current-badge{display:inline-block;margin-bottom:6px;padding:4px 8px;border-radius:20px;background:#30204b;color:#d8c5ff;font-size:10px}.premium-package-price{display:flex;align-items:baseline;gap:9px;flex-wrap:wrap;margin:0 0 13px}.premium-package-price>[data-price-current]{color:#c4a7ff;font-size:23px;font-weight:800}.premium-package-price del{color:#85858e;font-size:13px}.premium-package-price small{flex-basis:100%;color:#c4b5fd;font-size:11px}.premium-package-description{min-height:40px;margin-bottom:14px;color:#cbd5e1;font-size:12px}.premium-checkout-form{display:grid;gap:10px}.premium-checkout-form label{display:grid;gap:6px;color:#cbd5e1;font-size:11px}.premium-checkout-form select{width:100%;padding:9px;border:1px solid #cbd5e1;border-radius:7px;background:#fff;color:#111827}.premium-purchase-button{display:block;width:100%;padding:10px 12px;border:0;border-radius:8px;background:#8b5cf6;color:white;text-align:center;text-decoration:none;font-weight:700;cursor:pointer}.premium-purchase-button.is-disabled{background:#29252f;color:#96919f;cursor:not-allowed}.premium-plan-locked{color:#fca5a5;font-size:12px}@media(max-width:600px){.premium-grade-tabs{display:grid;grid-template-columns:repeat(2,minmax(0,1fr))}.premium-grade-tabs button{min-width:0}.premium-package-grid{grid-template-columns:1fr}}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const vnd = value => '₫' + new Intl.NumberFormat('vi-VN').format(value);

    document.querySelectorAll('[data-grade-tab]').forEach(tab => {
        tab.addEventListener('click', () => {
            const selected = tab.dataset.gradeTab;
            document.querySelectorAll('[data-grade-tab]').forEach(candidate => {
                const isSelected = candidate === tab;
                candidate.classList.toggle('is-selected', isSelected);
                candidate.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
            });
            document.querySelectorAll('[data-grade-panel]').forEach(panel => {
                panel.hidden = panel.dataset.gradePanel !== selected;
            });
        });
    });

    document.querySelectorAll('[data-premium-price]').forEach(price => {
        const article = price.closest('article');
        const select = article?.querySelector('select[name="coupon_code"]');

        const basePrice = Number(price.dataset.basePrice || 0);
        const promotionDiscount = Number(price.dataset.promotionDiscount || 0);
        const upgradeCredit = Number(price.dataset.upgradeCredit || 0);
        const current = price.querySelector('[data-price-current]');
        const original = price.querySelector('[data-price-original]');
        const discountLabel = price.querySelector('[data-price-label]');
        const promotionLabel = price.dataset.promotionLabel || '';

        const updateDisplayedPrice = () => {
            const selected = select?.options[select.selectedIndex];
            const couponDiscount = Number(selected?.dataset.discount || 0);
            const couponSelected = Boolean(selected?.value);
            const appliedDiscount = Math.min(basePrice, promotionDiscount + couponDiscount);
            const creditApplied = Math.min(upgradeCredit, Math.max(0, basePrice - appliedDiscount));
            const finalAmount = Math.max(0, basePrice - appliedDiscount - creditApplied);

            current.textContent = vnd(finalAmount);
            original.textContent = vnd(basePrice);
            original.hidden = appliedDiscount + creditApplied <= 0;
            const labels = [];
            if (promotionDiscount > 0 && promotionLabel) labels.push(promotionLabel);
            if (couponSelected && couponDiscount > 0) labels.push(`${selected.dataset.title} · Giảm ${selected.dataset.discountLabel || ''}`);
            if (creditApplied > 0) labels.push(`Số dư gói cũ −${vnd(creditApplied)}`);
            discountLabel.textContent = labels.join(' + ');
            discountLabel.hidden = labels.length === 0;
        };

        select?.addEventListener('change', updateDisplayedPrice);
        updateDisplayedPrice();
    });
});
</script>
@endsection

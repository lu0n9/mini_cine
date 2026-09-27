@extends('admin.layouts.master')
@section('content')
<section class="page premium-plan-admin">
    <div class="page-head">
        <div>
            <h3>Hạng và gói Premium</h3>
            <p>Chọn một hạng để xem, cập nhật hoặc thêm các gói theo tháng và năm.</p>
        </div>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

    <div class="premium-grade-list">
        @foreach($plansByGrade as $code => $gradePlans)
            @php
                $grade = $gradePlans->first();
                $gradeId = 'premium-grade-' . $loop->index;
                $gradeOpen = old('grade_code') === $code;
            @endphp
            <section class="panel premium-grade-panel">
                <button type="button" class="premium-grade-toggle {{ $gradeOpen ? 'is-open' : '' }}" aria-expanded="{{ $gradeOpen ? 'true' : 'false' }}" aria-controls="{{ $gradeId }}">
                    <span><strong>{{ $grade->name }}</strong><small>{{ $gradePlans->count() }} gói · {{ $code }}</small></span>
                    <span class="premium-grade-chevron" aria-hidden="true">⌄</span>
                </button>
                <div id="{{ $gradeId }}" class="premium-grade-body" @if(!$gradeOpen) hidden @endif>
                    <div class="premium-grade-toolbar">
                        <span>{{ $grade->code === 'premium_extra' ? 'Hạng có thể chia sẻ tài khoản.' : 'Hạng Premium tiêu chuẩn.' }}</span>
                        <button type="button" class="btn premium-add-plan-toggle" aria-expanded="{{ $gradeOpen ? 'true' : 'false' }}" aria-controls="{{ $gradeId }}-new">+ Thêm gói cho hạng này</button>
                    </div>

                    <form id="{{ $gradeId }}-new" class="premium-add-plan-form" action="{{ route('admin.premium.plans.store', ['code' => $code]) }}" method="POST" @if(!$gradeOpen) hidden @endif>
                        @csrf
                        <input type="hidden" name="grade_code" value="{{ $code }}">
                        <label>Tên gói<input name="name" value="{{ old('grade_code') === $code ? old('name') : $grade->name }}" maxlength="120" required></label>
                        <label>Chu kỳ
                            <select name="billing_period" required>
                                <option value="monthly" {{ old('grade_code') === $code && old('billing_period') === 'monthly' ? 'selected' : '' }}>Tháng</option>
                                <option value="yearly" {{ old('grade_code') === $code && old('billing_period') === 'yearly' ? 'selected' : '' }}>Năm</option>
                            </select>
                        </label>
                        <label>Giá (VND)<input type="number" name="price" value="{{ old('grade_code') === $code ? old('price') : '' }}" min="1000" max="100000000" required></label>
                        <label>Số tài khoản chia sẻ<input type="number" name="share_limit" value="{{ old('grade_code') === $code ? old('share_limit', $grade->share_limit) : $grade->share_limit }}" min="0" max="2" required></label>
                        <label class="premium-add-active"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" {{ old('grade_code') === $code ? (old('is_active') == '1' ? 'checked' : '') : 'checked' }}> Đang bán</label>
                        <button class="btn" type="submit">Tạo gói</button>
                    </form>

                    <div class="premium-plan-table-wrap">
                        <table class="premium-plan-table">
                            <thead><tr><th>Gói</th><th>Chu kỳ</th><th>Giá</th><th>Chia sẻ</th><th>Trạng thái</th><th>Cập nhật</th></tr></thead>
                            <tbody>
                                @foreach($gradePlans as $plan)
                                    <tr>
                                        <td><strong>{{ $plan->name }}</strong></td>
                                        <td>{{ $plan->billing_period === 'monthly' ? 'Tháng' : 'Năm' }}</td>
                                        <td>₫{{ number_format($plan->price, 0, ',', '.') }}</td>
                                        <td>{{ $plan->share_limit ?: 'Không' }}</td>
                                        <td><span class="premium-plan-state {{ $plan->is_active ? 'is-active' : '' }}">{{ $plan->is_active ? 'Đang bán' : 'Đã tắt' }}</span></td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.premium.plans.update', $plan) }}" class="premium-plan-update-form">
                                                @csrf @method('PUT')
                                                <input type="number" name="price" value="{{ $plan->price }}" min="1000" max="100000000" required aria-label="Giá {{ $plan->name }}">
                                                <label><input type="checkbox" name="is_active" value="1" @checked($plan->is_active)> Bán</label>
                                                <button class="mini" type="submit">Lưu</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        @endforeach
    </div>
</section>

<style>
.premium-grade-panel{margin-bottom:14px;overflow:hidden}.premium-grade-toggle{display:flex;width:100%;align-items:center;justify-content:space-between;padding:18px 22px;border:0;background:transparent;color:inherit;text-align:left;cursor:pointer}.premium-grade-toggle>span:first-child{display:grid;gap:5px}.premium-grade-toggle strong{font-size:17px}.premium-grade-toggle small{color:var(--muted,#9ca3af);font-size:12px}.premium-grade-chevron{font-size:23px;color:#c4b5fd;transition:transform .18s}.premium-grade-toggle.is-open .premium-grade-chevron{transform:rotate(180deg)}.premium-grade-body[hidden],.premium-add-plan-form[hidden]{display:none!important}.premium-grade-toolbar{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 20px;border-top:1px solid var(--line,#2b303a);color:var(--muted,#9ca3af);font-size:12px}.premium-add-plan-toggle{white-space:nowrap}.premium-add-plan-form{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;padding:16px 20px;background:rgba(124,58,237,.08);border-top:1px solid var(--line,#2b303a)}.premium-add-plan-form>label{display:grid;gap:6px;font-size:12px;font-weight:700}.premium-add-plan-form input:not([type=checkbox]),.premium-add-plan-form select,.premium-plan-update-form>input{width:100%;min-width:0;padding:9px 10px;border:1px solid #cbd5e1;border-radius:7px;background:#fff;color:#111827}.premium-add-active{display:flex!important;align-items:center;gap:7px}.premium-add-active input{accent-color:#7c3aed}.premium-plan-table-wrap{overflow-x:auto}.premium-plan-table{width:100%;border-collapse:collapse}.premium-plan-table th,.premium-plan-table td{padding:12px 16px;border-top:1px solid var(--line,#2b303a);text-align:left;vertical-align:middle}.premium-plan-table th{font-size:11px;color:var(--muted,#9ca3af);text-transform:uppercase}.premium-plan-state{font-size:12px;color:#fca5a5}.premium-plan-state.is-active{color:#86efac}.premium-plan-update-form{display:flex;align-items:center;gap:8px;min-width:250px}.premium-plan-update-form>input{max-width:130px}.premium-plan-update-form label{white-space:nowrap;font-size:12px}.premium-plan-update-form .mini{padding:7px 10px;cursor:pointer}@media(max-width:720px){.premium-add-plan-form{grid-template-columns:1fr 1fr}.premium-grade-toolbar{align-items:flex-start;flex-direction:column}.premium-plan-table{min-width:680px}}@media(max-width:460px){.premium-add-plan-form{grid-template-columns:1fr}}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.premium-grade-toggle').forEach(toggle => {
        toggle.addEventListener('click', () => {
            const body = document.getElementById(toggle.getAttribute('aria-controls'));
            const open = toggle.getAttribute('aria-expanded') === 'true';
            toggle.setAttribute('aria-expanded', open ? 'false' : 'true');
            toggle.classList.toggle('is-open', !open);
            body.hidden = open;
        });
    });
    document.querySelectorAll('.premium-add-plan-toggle').forEach(toggle => {
        toggle.addEventListener('click', () => {
            const form = document.getElementById(toggle.getAttribute('aria-controls'));
            const open = toggle.getAttribute('aria-expanded') === 'true';
            toggle.setAttribute('aria-expanded', open ? 'false' : 'true');
            form.hidden = open;
            if (!open) form.querySelector('[name="name"]')?.focus();
        });
    });
});
</script>
@endsection

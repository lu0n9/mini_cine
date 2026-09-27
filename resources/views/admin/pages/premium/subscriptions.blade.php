 @extends('admin.layouts.master')
@section('content')
<section id="subscriptions" class="page">
        <div class="page-head"><div><h3>Subscriptions</h3><p>Đăng ký của người dùng: active, expired, cancelled.</p></div></div>
        <div class="panel"><table>
          <thead><tr><th>User</th><th>Plan</th><th>Bắt đầu</th><th>Kết thúc</th><th>Trạng thái</th></tr></thead>
          <tbody>
            @forelse($subscriptions as $subscription)
              @php($expired = $subscription->status === 'active' && $subscription->ends_at?->isPast())
              <tr>
                <td>{{ $subscription->user?->name }}<br><small>{{ $subscription->user?->email }}</small></td>
                <td>{{ $subscription->plan?->name }} · {{ $subscription->plan?->billing_period === 'yearly' ? 'Năm' : 'Tháng' }}</td>
                <td>{{ $subscription->starts_at?->format('d/m/Y H:i') ?? '—' }}</td>
                <td>{{ $subscription->ends_at?->format('d/m/Y H:i') ?? '—' }}</td>
                <td><span class="status {{ $subscription->status !== 'active' || $expired ? 'off' : '' }}">{{ $expired ? 'Hết hạn' : ucfirst($subscription->status) }}</span></td>
              </tr>
            @empty<tr><td colspan="5">Chưa có đăng ký nào.</td></tr>@endforelse
          </tbody>
        </table></div>
        {{ $subscriptions->links() }}
      </section>
      @endsection

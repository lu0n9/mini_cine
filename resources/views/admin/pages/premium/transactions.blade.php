@extends('admin.layouts.master')
@section('content')
 <section id="transactions" class="page">
        <div class="page-head"><div><h3>Transactions</h3><p>Giao dịch, hóa đơn và hoàn tiền.</p></div></div>
        <div class="stats"><div class="stat"><span class="label">Doanh thu tháng</span><div class="num">₫{{ number_format($monthlyRevenue, 0, ',', '.') }}</div></div><div class="stat"><span class="label">Giao dịch</span><div class="num">{{ number_format($transactionCount) }}</div></div><div class="stat"><span class="label">Thành công</span><div class="num">{{ number_format($successCount) }}</div></div><div class="stat"><span class="label">Tỷ lệ thành công</span><div class="num">{{ $successRate }}%</div></div></div>
        <div class="panel"><table>
          <thead><tr><th>Mã GD</th><th>User</th><th>Số tiền</th><th>Phương thức</th><th>Trạng thái</th><th>Thời gian</th></tr></thead>
          <tbody>
            @forelse($transactions as $transaction)
              <tr><td>{{ $transaction->order_reference }}</td><td>{{ $transaction->subscription?->user?->email ?? '—' }}</td><td>₫{{ number_format($transaction->amount, 0, ',', '.') }}</td><td>{{ strtoupper($transaction->provider) }}</td><td><span class="status {{ $transaction->status === 'paid' ? '' : 'off' }}">{{ $transaction->status === 'paid' ? 'Thành công' : ($transaction->status === 'failed' ? 'Thất bại' : 'Đang chờ') }}</span></td><td>{{ $transaction->paid_at?->format('d/m/Y H:i') ?? $transaction->created_at?->format('d/m/Y H:i') }}</td></tr>
            @empty<tr><td colspan="6">Chưa có giao dịch nào.</td></tr>@endforelse
          </tbody>
        </table></div>
        {{ $transactions->links() }}
      </section>
      @endsection

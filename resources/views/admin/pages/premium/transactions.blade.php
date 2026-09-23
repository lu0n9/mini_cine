@extends('admin.layouts.master')
@section('content')
 <section id="transactions" class="page">
        <div class="page-head"><div><h3>Transactions</h3><p>Giao dịch, hóa đơn và hoàn tiền.</p></div></div>
        <div class="stats"><div class="stat"><span class="label">Doanh thu tháng</span><div class="num">₫420M</div></div><div class="stat"><span class="label">Giao dịch</span><div class="num">1.284</div></div><div class="stat"><span class="label">Hoàn tiền</span><div class="num">12</div></div><div class="stat"><span class="label">Thành công</span><div class="num">98.4%</div></div></div>
        <div class="panel"><table>
          <thead><tr><th>Mã GD</th><th>User</th><th>Số tiền</th><th>Phương thức</th><th>Trạng thái</th><th>Thời gian</th></tr></thead>
          <tbody>
            <tr><td>#TX-90231</td><td>@lanpham</td><td>₫990.000</td><td>Momo</td><td><span class="status">Thành công</span></td><td>Hôm nay</td></tr>
            <tr><td>#TX-90230</td><td>@huydo</td><td>₫99.000</td><td>VNPay</td><td><span class="status">Thành công</span></td><td>Hôm nay</td></tr>
            <tr><td>#TX-90228</td><td>@guest22</td><td>₫99.000</td><td>Thẻ</td><td><span class="status off">Thất bại</span></td><td>Hôm qua</td></tr>
          </tbody>
        </table></div>
      </section>
      @endsection
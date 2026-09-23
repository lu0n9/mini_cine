 @extends('admin.layouts.master')
@section('content')
<section id="subscriptions" class="page">
        <div class="page-head"><div><h3>Subscriptions</h3><p>Đăng ký của người dùng: active, expired, cancelled.</p></div></div>
        <div class="table-tools"><span class="filter on">Active</span><span class="filter">Expired</span><span class="filter">Cancelled</span></div>
        <div class="panel"><table>
          <thead><tr><th>User</th><th>Plan</th><th>Bắt đầu</th><th>Kết thúc</th><th>Trạng thái</th></tr></thead>
          <tbody>
            <tr><td>@lanpham</td><td>Premium Năm</td><td>01/01/2026</td><td>01/01/2027</td><td><span class="status">Active</span></td></tr>
            <tr><td>@huydo</td><td>Premium Tháng</td><td>05/09/2026</td><td>05/10/2026</td><td><span class="status">Active</span></td></tr>
            <tr><td>@sonle</td><td>Premium Tháng</td><td>01/07/2026</td><td>01/08/2026</td><td><span class="status off">Expired</span></td></tr>
          </tbody>
        </table></div>
      </section>
      @endsection
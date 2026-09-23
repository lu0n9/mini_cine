 @extends('admin.layouts.master')
@section('content')
  <section id="notifications" class="page">
        <div class="page-head"><div><h3>Notifications</h3><p>Tạo và gửi thông báo tới tất cả user, theo role hoặc user cụ thể.</p></div></div>
        <div class="grid-2">
          <div class="panel"><div class="panel-head"><h4>Tạo thông báo</h4></div><div class="panel-body"><form class="form-grid" onsubmit="return false" style="grid-template-columns:1fr">
            <div class="field"><label>Tiêu đề</label><input placeholder="VD: Tập mới đã lên sóng"></div>
            <div class="field"><label>Loại</label><select><option>New episode</option><option>New movie</option><option>System</option><option>Promotion</option><option>Maintenance</option></select></div>
            <div class="field"><label>Gửi tới</label><select><option>Tất cả user</option><option>Theo role</option><option>User cụ thể</option></select></div>
            <div class="field"><label>Nội dung</label><textarea placeholder="Nội dung thông báo..."></textarea></div>
            <div class="form-actions"><button class="btn">Gửi thông báo</button></div>
          </form></div></div>
          <div class="panel"><div class="panel-head"><h4>Lịch sử gửi</h4></div><div class="panel-body activity">
            <div class="act"><div class="dot">✉</div><div class="txt"><b>Tập 12 Crimson Vale</b> đã lên sóng<small>Gửi 12.4K user · hôm nay</small></div></div>
            <div class="act"><div class="dot">%</div><div class="txt"><b>Ưu đãi Premium -30%</b><small>Gửi Free users · hôm qua</small></div></div>
            <div class="act"><div class="dot">⚙</div><div class="txt"><b>Bảo trì hệ thống 02:00</b><small>Gửi tất cả · 3 ngày trước</small></div></div>
          </div></div>
        </div>
      </section>
      @endsection
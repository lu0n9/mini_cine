   @extends('admin.layouts.master')
@section('content')
   <section id="email" class="page">
        <div class="page-head"><div><h3>Email</h3><p>Cấu hình SMTP và mẫu email hệ thống.</p></div></div>
        <div class="grid-2">
          <div class="panel"><div class="panel-head"><h4>SMTP Settings</h4></div><div class="panel-body"><form class="form-grid" onsubmit="return false" style="grid-template-columns:1fr 1fr">
            <div class="field"><label>SMTP Host</label><input placeholder="smtp.example.com"></div>
            <div class="field"><label>Port</label><input placeholder="587"></div>
            <div class="field"><label>Username</label><input></div>
            <div class="field"><label>Password</label><input type="password"></div>
            <div class="field full"><label>From Email</label><input placeholder="no-reply@cineadmin.vn"></div>
            <div class="form-actions"><button class="btn ghost">Gửi thử</button><button class="btn">Lưu</button></div>
          </form></div></div>
          <div class="panel"><div class="panel-head"><h4>Email Templates</h4></div><div class="panel-body activity">
            <div class="act"><div class="txt"><b>Welcome email</b><small>Kích hoạt khi đăng ký</small></div></div>
            <div class="act"><div class="txt"><b>Verify email</b><small>Xác thực tài khoản</small></div></div>
            <div class="act"><div class="txt"><b>Reset password</b><small>Khôi phục mật khẩu</small></div></div>
            <div class="act"><div class="txt"><b>New episode notification</b><small>Tập mới của phim theo dõi</small></div></div>
          </div></div>
        </div>
      </section>
      @endsection
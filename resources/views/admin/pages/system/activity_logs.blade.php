@extends('admin.layouts.master')
@section('content')
<section id="logs" class="page">
        <div class="page-head"><div><h3>Activity Logs</h3><p>Nhật ký thao tác của quản trị viên.</p></div></div>
        <div class="panel"><table>
          <thead><tr><th>Người thực hiện</th><th>Hành động</th><th>Đối tượng</th><th>IP</th><th>Thời gian</th></tr></thead>
          <tbody>
            <tr><td>Minh Trần</td><td>Thêm phim</td><td>Movie #248 (Last Horizon)</td><td>103.21.x.x</td><td>15 phút trước</td></tr>
            <tr><td>Huy Đỗ</td><td>Cập nhật tập</td><td>Episode #5312</td><td>118.70.x.x</td><td>1 giờ trước</td></tr>
            <tr><td>Minh Trần</td><td>Đổi role user</td><td>User @huydo → Editor</td><td>103.21.x.x</td><td>2 giờ trước</td></tr>
            <tr><td>System</td><td>Xóa file tạm</td><td>temp-upload-441.mp4</td><td>—</td><td>3 giờ trước</td></tr>
          </tbody>
        </table></div>
      </section>
      @endsection
 @extends('admin.layouts.master')
@section('content')
 <section id="homepage" class="page">
        <div class="page-head"><div><h3>Homepage</h3><p>Quản lý các section hiển thị trên trang chủ và thứ tự.</p></div><button class="btn">+ Thêm section</button></div>
        <div class="panel"><table>
          <thead><tr><th>Section</th><th>Kiểu</th><th>Giới hạn</th><th>Thứ tự</th><th>Trạng thái</th><th></th></tr></thead>
          <tbody>
            <tr><td>Phim mới cập nhật</td><td>Tự động</td><td>12</td><td>1</td><td><span class="status">Hiển thị</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td>Phim nổi bật</td><td>Thủ công</td><td>8</td><td>2</td><td><span class="status">Hiển thị</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td>Anime mới</td><td>Theo thể loại</td><td>10</td><td>3</td><td><span class="status">Hiển thị</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td>Phim bộ Hàn Quốc</td><td>Theo quốc gia</td><td>10</td><td>4</td><td><span class="status off">Ẩn</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
          </tbody>
        </table></div>
      </section>
      @endsection
@extends('admin.layouts.master')
@section('content')
<section id="reviews" class="page">
        <div class="page-head"><div><h3>Reviews</h3><p>Đánh giá dạng bài viết của người dùng, xử lý spam và khôi phục.</p></div></div>
        <div class="panel"><table>
          <thead><tr><th>User</th><th>Phim</th><th>Review</th><th>Điểm</th><th>Trạng thái</th><th></th></tr></thead>
          <tbody>
            <tr><td>@lanpham</td><td>Silent Echo</td><td>Diễn xuất tuyệt vời, hình ảnh đen trắng đầy cảm xúc...</td><td class="rating">4.8</td><td><span class="status">Hiển thị</span></td><td><div class="row-actions"><button class="mini">◑</button><button class="mini">✕</button></div></td></tr>
            <tr><td>@guest22</td><td>Crimson Vale</td><td>Nội dung hơi chậm ở giữa phim.</td><td class="rating">3.5</td><td><span class="status warn">Chờ duyệt</span></td><td><div class="row-actions"><button class="mini">✔</button><button class="mini">✕</button></div></td></tr>
          </tbody>
        </table></div>
      </section>
      @endsection
 @extends('admin.layouts.master')
@section('content')
 <section id="banners" class="page">
        <div class="page-head"><div><h3>Banners / Slider</h3><p>Quản lý banner trang chủ với ưu tiên và thời gian hiển thị.</p></div><button class="btn">+ Thêm banner</button></div>
        <div class="panel"><table>
          <thead><tr><th>Banner</th><th>Phim</th><th>Thời gian</th><th>Ưu tiên</th><th>Trạng thái</th><th></th></tr></thead>
          <tbody>
            <tr><td><div class="movie-cell sm"><img src="/posters/last-horizon.png" alt=""><span class="mt">Ra mắt Last Horizon</span></div></td><td>Last Horizon</td><td>01/09 – 30/09</td><td>1</td><td><span class="status">Hiển thị</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td><div class="movie-cell sm"><img src="/posters/neon-nights.png" alt=""><span class="mt">Ưu đãi Premium</span></div></td><td>—</td><td>10/09 – 20/09</td><td>2</td><td><span class="status off">Hết hạn</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
          </tbody>
        </table></div>
      </section>
      @endsection
   
   @extends('admin.layouts.master')
@section('content')
<section id="sitemap" class="page">
        <div class="page-head"><div><h3>Sitemap</h3><p>Tạo và quản lý sitemap cho từng loại nội dung.</p></div><button class="btn">Generate toàn bộ</button></div>
        <div class="panel"><table>
          <thead><tr><th>Sitemap</th><th>URL</th><th>Số URL</th><th>Cập nhật</th><th></th></tr></thead>
          <tbody>
            <tr><td>Movie sitemap</td><td>/sitemap-movies.xml</td><td>248</td><td>Hôm nay</td><td><div class="row-actions"><button class="mini">↻</button></div></td></tr>
            <tr><td>Genre sitemap</td><td>/sitemap-genres.xml</td><td>18</td><td>Hôm nay</td><td><div class="row-actions"><button class="mini">↻</button></div></td></tr>
            <tr><td>Episode sitemap</td><td>/sitemap-episodes.xml</td><td>5.312</td><td>Hôm qua</td><td><div class="row-actions"><button class="mini">↻</button></div></td></tr>
            <tr><td>Actor sitemap</td><td>/sitemap-actors.xml</td><td>640</td><td>3 ngày trước</td><td><div class="row-actions"><button class="mini">↻</button></div></td></tr>
          </tbody>
        </table></div>
      </section>
      @endsection
 @extends('admin.layouts.master')
@section('content')
 <section id="pages" class="page">
        <div class="page-head"><div><h3>Trang tĩnh</h3><p>About, Contact, Privacy, Terms, DMCA, FAQ — publish/draft và SEO.</p></div><button class="btn">+ Thêm trang</button></div>
        <div class="panel"><table>
          <thead><tr><th>Tiêu đề</th><th>Slug</th><th>Cập nhật</th><th>Trạng thái</th><th></th></tr></thead>
          <tbody>
            <tr><td>Về chúng tôi</td><td>/about</td><td>02/09/2026</td><td><span class="status">Published</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td>Chính sách bảo mật</td><td>/privacy</td><td>28/08/2026</td><td><span class="status">Published</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td>Điều khoản dịch vụ</td><td>/terms</td><td>28/08/2026</td><td><span class="status">Published</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td>DMCA</td><td>/dmca</td><td>—</td><td><span class="status off">Draft</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td>FAQ</td><td>/faq</td><td>15/08/2026</td><td><span class="status">Published</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
          </tbody>
        </table></div>
      </section>
      @endsection
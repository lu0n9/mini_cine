 
 @extends('admin.layouts.master')
@section('content')
<section id="seo" class="page">
        <div class="page-head"><div><h3>SEO Settings</h3><p>Cấu hình SEO toàn website.</p></div></div>
        <div class="panel"><div class="panel-body"><form class="form-grid" onsubmit="return false">
          <div class="field"><label>Site Title</label><input placeholder="CineAdmin - Xem phim online"></div>
          <div class="field"><label>Canonical URL</label><input placeholder="https://cineadmin.vn"></div>
          <div class="field full"><label>Site Description</label><textarea style="min-height:70px"></textarea></div>
          <div class="field full"><label>Keywords</label><input placeholder="xem phim, phim online, phim hd"></div>
          <div class="field"><label>Google Verification</label><input></div>
          <div class="field"><label>Bing Verification</label><input></div>
          <div class="upload"><b>Logo &amp; Favicon</b> — kéo thả file vào đây</div>
          <div class="form-actions"><button class="btn">Lưu SEO</button></div>
        </form></div></div>
      </section>
      @endsection
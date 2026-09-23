@extends('admin.layouts.master')
@section('content')
<section id="storage" class="page">
        <div class="page-head"><div><h3>Storage</h3><p>Quản lý file: ảnh, video, phụ đề, poster, backdrop.</p></div><button class="btn">+ Upload file</button></div>
        <div class="panel"><div class="panel-body">
          <div style="display:flex;justify-content:space-between;margin-bottom:8px;font-size:14px"><b>Dung lượng đã dùng</b><span>612 GB / 1 TB</span></div>
          <div class="prog"><i style="width:61%"></i></div>
        </div></div>
        <div class="grid-3" style="margin-top:18px">
          <div class="panel"><div class="panel-body"><h4>Posters</h4><p class="hint">248 file · 3.2 GB</p></div></div>
          <div class="panel"><div class="panel-body"><h4>Videos</h4><p class="hint">5.312 file · 588 GB</p></div></div>
          <div class="panel"><div class="panel-body"><h4>Subtitles</h4><p class="hint">9.140 file · 420 MB</p></div></div>
        </div>
        <div class="panel"><div class="panel-head"><h4>File chưa dùng</h4><a class="link">Dọn dẹp</a></div><div class="panel-body activity"><div class="act"><div class="txt"><b>old-banner-2024.jpg</b><small>2.1 MB · không còn liên kết</small></div></div><div class="act"><div class="txt"><b>temp-upload-441.mp4</b><small>1.2 GB · file tạm</small></div></div></div></div>
      </section>
      @endsection
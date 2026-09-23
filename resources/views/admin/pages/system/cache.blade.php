
@extends('admin.layouts.master')
@section('content')
<section id="cache" class="page">
        <div class="page-head"><div><h3>Cache</h3><p>Xóa các loại cache của ứng dụng.</p></div></div>
        <div class="panel"><div class="panel-body">
          <div class="setting-row"><div class="st-txt"><strong>Application cache</strong><small>Dữ liệu ứng dụng tạm</small></div><button class="btn ghost sm">Clear</button></div>
          <div class="setting-row"><div class="st-txt"><strong>Config cache</strong><small>Cấu hình hệ thống</small></div><button class="btn ghost sm">Clear</button></div>
          <div class="setting-row"><div class="st-txt"><strong>Route cache</strong><small>Định tuyến</small></div><button class="btn ghost sm">Clear</button></div>
          <div class="setting-row"><div class="st-txt"><strong>View cache</strong><small>Template đã biên dịch</small></div><button class="btn ghost sm">Clear</button></div>
          <div class="setting-row"><div class="st-txt"><strong>Toàn bộ cache</strong><small>Xóa tất cả các loại cache</small></div><button class="btn sm">Clear All</button></div>
        </div></div>
      </section>
      @endsection
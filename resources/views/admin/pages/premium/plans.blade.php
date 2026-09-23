 @extends('admin.layouts.master')
@section('content')
 <section id="plans" class="page">
        <div class="page-head"><div><h3>Plans</h3><p>Các gói Premium và quyền lợi.</p></div><button class="btn">+ Thêm plan</button></div>
        <div class="grid-3">
          <div class="panel"><div class="panel-body"><span class="tag">Free</span><div class="num" style="font-size:26px;font-weight:800;margin:12px 0">₫0</div><p class="hint">Quảng cáo · 720p · 1 thiết bị</p></div></div>
          <div class="panel"><div class="panel-body"><span class="tag solid">Premium Tháng</span><div class="num" style="font-size:26px;font-weight:800;margin:12px 0">₫99K</div><p class="hint">Không quảng cáo · 4K · 4 thiết bị</p></div></div>
          <div class="panel"><div class="panel-body"><span class="tag solid">Premium Năm</span><div class="num" style="font-size:26px;font-weight:800;margin:12px 0">₫990K</div><p class="hint">Tất cả quyền lợi · tiết kiệm 17%</p></div></div>
        </div>
      </section>
      @endsection
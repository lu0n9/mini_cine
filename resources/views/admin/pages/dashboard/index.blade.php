@extends('admin.layouts.master')
@section('content')
<section id="dashboard" class="page">
        <div class="page-head"><div>
          <h3>Bảng điều khiển</h3>
          <p>Tổng quan hoạt động nền tảng xem phim của bạn hôm nay.</p>
        </div></div>

        <div class="stats">
          <div class="stat"><div class="top"><span class="label">Tổng phim</span><span class="chip"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M7 4v16M17 4v16"/></svg></span></div><div class="num">248</div><div class="delta"><b>+12</b> phim trong tháng</div></div>
          <div class="stat"><div class="top"><span class="label">Tổng tập phim</span><span class="chip"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg></span></div><div class="num">5.312</div><div class="delta"><b>+184</b> tập tuần này</div></div>
          <div class="stat"><div class="top"><span class="label">Người dùng</span><span class="chip"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="8" r="4"/><path d="M2 21c0-4 3-6 7-6s7 2 7 6"/></svg></span></div><div class="num">12.4K</div><div class="delta"><b>+312</b> người mới</div></div>
          <div class="stat"><div class="top"><span class="label">Lượt xem hôm nay</span><span class="chip"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7"/><circle cx="12" cy="12" r="3"/></svg></span></div><div class="num">86.7K</div><div class="delta"><b>+21%</b> giờ cao điểm 20:00</div></div>
        </div>

        <div class="grid-2">
          <div class="panel">
            <div class="panel-head"><h4>Lượt xem theo tháng</h4><span class="legend"><span><i class="d"></i>Premium</span><span><i class="l"></i>Miễn phí</span></span></div>
            <div class="panel-body">
              <div class="chart">
                <div class="bar-wrap"><div class="bar" style="height:45%"></div><span class="bar-x">T1</span></div>
                <div class="bar-wrap"><div class="bar" style="height:62%"></div><span class="bar-x">T2</span></div>
                <div class="bar-wrap"><div class="bar" style="height:38%"></div><span class="bar-x">T3</span></div>
                <div class="bar-wrap"><div class="bar" style="height:74%"></div><span class="bar-x">T4</span></div>
                <div class="bar-wrap"><div class="bar alt" style="height:55%"></div><span class="bar-x">T5</span></div>
                <div class="bar-wrap"><div class="bar" style="height:88%"></div><span class="bar-x">T6</span></div>
                <div class="bar-wrap"><div class="bar" style="height:67%"></div><span class="bar-x">T7</span></div>
                <div class="bar-wrap"><div class="bar alt" style="height:71%"></div><span class="bar-x">T8</span></div>
                <div class="bar-wrap"><div class="bar" style="height:82%"></div><span class="bar-x">T9</span></div>
                <div class="bar-wrap"><div class="bar" style="height:60%"></div><span class="bar-x">T10</span></div>
                <div class="bar-wrap"><div class="bar" style="height:93%"></div><span class="bar-x">T11</span></div>
                <div class="bar-wrap"><div class="bar" style="height:100%"></div><span class="bar-x">T12</span></div>
              </div>
            </div>
          </div>
          <div class="panel">
            <div class="panel-head"><h4>Hoạt động gần đây</h4><a href="#logs" class="link">Xem tất cả</a></div>
            <div class="panel-body activity">
              <div class="act"><div class="dot">+</div><div class="txt"><b>Phim mới</b> "Last Horizon" đã được thêm<small>15 phút trước · Minh Trần</small></div></div>
              <div class="act"><div class="dot">★</div><div class="txt"><b>Đánh giá mới</b> 4.8★ cho "Silent Echo"<small>42 phút trước</small></div></div>
              <div class="act"><div class="dot">!</div><div class="txt"><b>Báo lỗi</b> Link chết tập 4 "Iron Verdict"<small>1 giờ trước</small></div></div>
              <div class="act"><div class="dot">✎</div><div class="txt"><b>Cập nhật</b> Thêm tập 12 "Crimson Vale"<small>2 giờ trước</small></div></div>
              <div class="act"><div class="dot">₫</div><div class="txt"><b>Thanh toán</b> Gói Premium năm — ₫990.000<small>3 giờ trước</small></div></div>
            </div>
          </div>
        </div>

        <div class="panel">
          <div class="panel-head"><h4>Phim được xem nhiều nhất</h4><a href="#popular-stats" class="link">Thống kê</a></div>
          <div class="panel-body">
            <div class="poster-grid">
              <div class="poster"><div class="pic"><span class="rk">#1</span><img src="/posters/neon-nights.png" alt="Neon Nights"></div><div class="meta"><h5>Neon Nights</h5><div class="sub"><span>Hành động</span><span>1.2M</span></div></div></div>
              <div class="poster"><div class="pic"><span class="rk">#2</span><img src="/posters/last-horizon.png" alt="Last Horizon"></div><div class="meta"><h5>Last Horizon</h5><div class="sub"><span>Khoa học</span><span>980K</span></div></div></div>
              <div class="poster"><div class="pic"><span class="rk">#3</span><img src="/posters/silent-echo.png" alt="Silent Echo"></div><div class="meta"><h5>Silent Echo</h5><div class="sub"><span>Chính kịch</span><span>870K</span></div></div></div>
              <div class="poster"><div class="pic"><span class="rk">#4</span><img src="/posters/iron-verdict.png" alt="Iron Verdict"></div><div class="meta"><h5>Iron Verdict</h5><div class="sub"><span>Tội phạm</span><span>760K</span></div></div></div>
            </div>
          </div>
        </div>
      </section>
      @endsection
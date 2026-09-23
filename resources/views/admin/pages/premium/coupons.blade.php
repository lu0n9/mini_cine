 @extends('admin.layouts.master')
@section('content')
 <section id="coupons" class="page">
        <div class="page-head"><div><h3>Coupons</h3><p>Mã giảm giá kèm điều kiện và giới hạn sử dụng.</p></div><button class="btn">+ Thêm coupon</button></div>
        <div class="panel"><table>
          <thead><tr><th>Code</th><th>Giảm</th><th>Thời gian</th><th>Đã dùng / Giới hạn</th><th>Trạng thái</th><th></th></tr></thead>
          <tbody>
            <tr><td><span class="tag solid">WELCOME30</span></td><td>30%</td><td>01/09 – 30/09</td><td>420 / 1000</td><td><span class="status">Bật</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td><span class="tag">TET2026</span></td><td>₫50.000</td><td>Hết hạn</td><td>980 / 1000</td><td><span class="status off">Tắt</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
          </tbody>
        </table></div>
      </section>
      @endsection
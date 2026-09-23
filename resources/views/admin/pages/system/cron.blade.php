@extends('admin.layouts.master')
@section('content')
<section id="cron" class="page">
        <div class="page-head"><div><h3>Cron Jobs</h3><p>Tác vụ định kỳ của hệ thống.</p></div></div>
        <div class="panel"><table>
          <thead><tr><th>Job</th><th>Lịch</th><th>Lần chạy cuối</th><th>Trạng thái</th><th></th></tr></thead>
          <tbody>
            <tr><td>Backup database</td><td>Hằng ngày 02:00</td><td>Hôm nay 02:00</td><td><span class="status">OK</span></td><td><div class="row-actions"><button class="mini" title="Chạy ngay">▶</button></div></td></tr>
            <tr><td>Update movie metadata</td><td>Mỗi 6 giờ</td><td>4 giờ trước</td><td><span class="status">OK</span></td><td><div class="row-actions"><button class="mini">▶</button></div></td></tr>
            <tr><td>Generate sitemap</td><td>Hằng ngày 03:00</td><td>Hôm nay 03:00</td><td><span class="status">OK</span></td><td><div class="row-actions"><button class="mini">▶</button></div></td></tr>
            <tr><td>Clean expired sessions</td><td>Mỗi giờ</td><td>20 phút trước</td><td><span class="status warn">Đang chạy</span></td><td><div class="row-actions"><button class="mini">▶</button></div></td></tr>
          </tbody>
        </table></div>
      </section>
      @endsection
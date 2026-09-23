@extends('admin.layouts.master')
@section('content')
<section id="requests" class="page">
        <div class="page-head"><div><h3>Movie Requests</h3><p>Yêu cầu phim từ người dùng — duyệt, xử lý, từ chối.</p></div></div>
        <div class="table-tools"><span class="filter on">Pending</span><span class="filter">Processing</span><span class="filter">Completed</span><span class="filter">Rejected</span></div>
        <div class="panel"><table>
          <thead><tr><th>Tên phim</th><th>IMDb / TMDB</th><th>User</th><th>Ghi chú</th><th>Trạng thái</th><th></th></tr></thead>
          <tbody>
            <tr><td>Dune: Part Three</td><td>tt1234567</td><td>@lanpham</td><td>Rất mong chờ phần này</td><td><span class="status warn">Pending</span></td><td><div class="row-actions"><button class="mini">✔</button><button class="mini">✕</button></div></td></tr>
            <tr><td>The Batman 2</td><td>tt7654321</td><td>@huydo</td><td>—</td><td><span class="status warn">Processing</span></td><td><div class="row-actions"><button class="mini">✔</button><button class="mini">✕</button></div></td></tr>
          </tbody>
        </table></div>
      </section>
      @endsection
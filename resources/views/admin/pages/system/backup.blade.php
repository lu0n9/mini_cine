 @extends('admin.layouts.master')
@section('content')
 <section id="backup" class="page">
        <div class="page-head"><div><h3>Backup</h3><p>Sao lưu database và file, khôi phục khi cần.</p></div><button class="btn">+ Tạo backup</button></div>
        <div class="panel"><table>
          <thead><tr><th>Tên backup</th><th>Loại</th><th>Kích thước</th><th>Thời gian</th><th></th></tr></thead>
          <tbody>
            <tr><td>backup-2026-09-08.sql</td><td>Database</td><td>842 MB</td><td>Hôm nay 02:00</td><td><div class="row-actions"><button class="mini" title="Tải">↓</button><button class="mini" title="Khôi phục">↻</button><button class="mini">✕</button></div></td></tr>
            <tr><td>backup-files-2026-09-01.zip</td><td>Files</td><td>58 GB</td><td>01/09 02:00</td><td><div class="row-actions"><button class="mini">↓</button><button class="mini">↻</button><button class="mini">✕</button></div></td></tr>
          </tbody>
        </table></div>
      </section>
      @endsection
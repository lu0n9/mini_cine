  @extends('admin.layouts.master')
@section('content')
<section id="api" class="page">
        <div class="page-head"><div><h3>API Management</h3><p>Quản lý API keys, giới hạn tần suất và nhật ký sử dụng.</p></div><button class="btn">+ Tạo API key</button></div>
        <div class="panel"><table>
          <thead><tr><th>Tên</th><th>Key</th><th>Rate limit</th><th>Sử dụng hôm nay</th><th>Trạng thái</th><th></th></tr></thead>
          <tbody>
            <tr><td>Mobile App</td><td>sk_live_••••4f2a</td><td>1000/phút</td><td>42.1K</td><td><span class="status">Active</span></td><td><div class="row-actions"><button class="mini" title="Thu hồi">✕</button></div></td></tr>
            <tr><td>Partner CDN</td><td>sk_live_••••9b1c</td><td>500/phút</td><td>18.4K</td><td><span class="status">Active</span></td><td><div class="row-actions"><button class="mini">✕</button></div></td></tr>
          </tbody>
        </table></div>
      </section>
      @endsection
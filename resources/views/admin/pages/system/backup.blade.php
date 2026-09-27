@extends('admin.layouts.master')

@section('content')
<section id="backup" class="page">
    <div class="page-head">
        <div>
            <h3>Backup</h3>
            <p>Sao lưu database và file upload để có thể tải xuống hoặc khôi phục khi cần.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success" style="margin-bottom:16px">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom:16px">{{ $errors->first() }}</div>
    @endif

    <div class="panel" style="padding:20px;margin-bottom:20px">
        <h4 style="margin:0 0 8px">Tạo bản sao lưu</h4>
        <p style="margin:0 0 16px;color:#7b8495">File được lưu riêng trong máy chủ tại <code>storage/app/private/backups</code>.</p>
        <div style="display:flex;gap:10px;flex-wrap:wrap">
            <form method="POST" action="{{ route('admin.system.backup.create') }}">
                @csrf
                <input type="hidden" name="type" value="database">
                <button class="btn" type="submit">Sao lưu database</button>
            </form>
            <form method="POST" action="{{ route('admin.system.backup.create') }}">
                @csrf
                <input type="hidden" name="type" value="files">
                <button class="btn" type="submit">Sao lưu file upload</button>
            </form>
            <form method="POST" action="{{ route('admin.system.backup.create') }}">
                @csrf
                <input type="hidden" name="type" value="full">
                <button class="btn" type="submit">Sao lưu toàn bộ</button>
            </form>
        </div>
        <small style="display:block;margin-top:12px;color:#7b8495">Backup file gồm <code>storage/app/public</code> và <code>public/uploads</code>. Database MySQL/MariaDB cần có mysqldump; SQLite được sao chép trực tiếp.</small>
    </div>

    <div class="panel">
        <div style="padding:18px 20px 0">
            <h4 style="margin:0">Các bản backup</h4>
        </div>
        <div style="overflow-x:auto">
            <table>
                <thead>
                    <tr><th>Tên backup</th><th>Loại</th><th>Kích thước</th><th>Thời gian tạo</th><th>Thao tác</th></tr>
                </thead>
                <tbody>
                    @forelse ($backups as $backup)
                        <tr>
                            <td><strong>{{ $backup['name'] }}</strong></td>
                            <td>{{ $backup['type'] }}</td>
                            <td>{{ $backup['size'] }}</td>
                            <td>{{ $backup['created_at']->timezone(config('app.timezone'))->format('d/m/Y H:i:s') }}</td>
                            <td>
                                <div class="row-actions" style="display:flex;gap:6px;align-items:center">
                                    <a class="mini" title="Tải xuống" aria-label="Tải xuống" href="{{ route('admin.system.backup.download', $backup['name']) }}">↓</a>
                                    <form method="POST" action="{{ route('admin.system.backup.restore', $backup['name']) }}" onsubmit="return confirmBackupRestore(this)">
                                        @csrf
                                        <input type="hidden" name="confirmation" value="">
                                        <button class="mini" type="submit" title="Khôi phục" aria-label="Khôi phục">↻</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.system.backup.delete', $backup['name']) }}" onsubmit="return confirm('Xóa vĩnh viễn bản backup này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="mini" type="submit" title="Xóa" aria-label="Xóa">✕</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center;padding:28px;color:#7b8495">Chưa có bản backup nào. Tạo bản đầu tiên ở phía trên.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

<script>
    function confirmBackupRestore(form) {
        const typed = window.prompt('Khôi phục sẽ thay thế dữ liệu hiện tại. Hệ thống tạo backup hiện trạng trước. Nhập RESTORE để tiếp tục:');
        if (typed !== 'RESTORE') return false;
        form.elements.confirmation.value = typed;
        return true;
    }
</script>
@endsection

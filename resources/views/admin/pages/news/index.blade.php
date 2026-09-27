@extends('admin.layouts.master')

@section('content')
<section class="page">
    <div class="page-head">
        <div>
            <h3>Quản lý tin tức</h3>
            <p>Tạo, chỉnh sửa và xuất bản bài viết trên trang tin tức.</p>
        </div>
        <a href="{{ route('admin.news.create') }}" class="btn">+ Viết tin mới</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.news.index') }}" class="table-tools">
        <span class="mini-search"><input name="search" value="{{ request('search') }}" placeholder="Tìm tiêu đề hoặc mô tả"></span>
        <select class="filter" name="status" aria-label="Lọc theo trạng thái">
            <option value="">Tất cả trạng thái</option>
            <option value="published" @selected(request('status') === 'published')>Đã xuất bản</option>
            <option value="draft" @selected(request('status') === 'draft')>Bản nháp</option>
        </select>
        <button type="submit" class="filter on">Lọc</button>
        <a class="filter" href="{{ route('admin.news.index') }}">Xóa lọc</a>
    </form>

    <div class="panel">
        <div style="overflow-x:auto">
            <table>
                <thead><tr><th>Tin tức</th><th>Slug</th><th>Ngày đăng</th><th>Trạng thái</th><th></th></tr></thead>
                <tbody>
                    @forelse($news as $article)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:12px;min-width:220px">
                                    @if($article->cover_image)
                                        <img src="{{ asset('storage/' . $article->cover_image) }}" alt="" style="width:64px;height:44px;object-fit:cover;border-radius:6px">
                                    @endif
                                    <strong>{{ $article->title }}</strong>
                                </div>
                            </td>
                            <td>/news/{{ $article->slug }}</td>
                            <td>{{ $article->published_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td><span class="status {{ $article->status === 'published' ? '' : 'off' }}">{{ $article->status === 'published' ? 'Đã xuất bản' : 'Bản nháp' }}</span></td>
                            <td><div class="row-actions">
                                <a class="mini" href="{{ route('admin.news.edit', $article) }}" title="Chỉnh sửa">✎</a>
                                @if($article->status === 'published')<a class="mini" href="{{ route('news.show', $article->slug) }}" target="_blank" rel="noopener" title="Xem tin">↗</a>@endif
                                <form action="{{ route('admin.news.destroy', $article) }}" method="POST" onsubmit="return confirm('Xóa bài viết này?')">@csrf @method('DELETE')<button class="mini" type="submit" title="Xóa">✕</button></form>
                            </div></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center;padding:32px">Chưa có bài viết nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($news->hasPages())<div class="pagination">{{ $news->links('pagination::custom') }}</div>@endif
</section>
@endsection

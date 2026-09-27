@extends('admin.layouts.master')
@section('content')
<section id="movies" class="page">
    <div class="page-head">
      <div>
        <h3>Tất cả phim</h3>
        <p>Quản lý toàn bộ kho phim: thêm, sửa, ẩn/hiện và trạng thái.</p>
      </div>
      <a href="{{ route('admin.movies.create') }}" class="btn">+ Thêm phim</a>
    </div>

    <!-- Bộ lọc & Tìm kiếm -->
    <form method="GET" action="{{ route('admin.movies.index') }}" class="table-tools">
      <a href="{{ route('admin.movies.index') }}" class="filter {{ !request('type') || request('type') == 'all' ? 'on' : '' }}">Tất cả</a>
      <a href="{{ route('admin.movies.index', ['type' => 'single']) }}" class="filter {{ request('type') == 'single' ? 'on' : '' }}">Phim lẻ</a>
      <a href="{{ route('admin.movies.index', ['type' => 'series']) }}" class="filter {{ request('type') == 'series' ? 'on' : '' }}">Phim bộ</a>
      <a href="{{ route('admin.movies.index', ['type' => 'anime']) }}" class="filter {{ request('type') == 'anime' ? 'on' : '' }}">Anime</a>
      <a href="{{ route('admin.movies.index', ['type' => 'tvshow']) }}" class="filter {{ request('type') == 'tvshow' ? 'on' : '' }}">TV Show</a>
      
      <span class="spacer"></span>
      
      <span class="mini-search">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="7"/><path d="m21 21-4-4"/>
        </svg>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm phim..." onkeydown="if(event.key==='Enter') this.form.submit()">
      </span>
    </form>

    <div class="panel">
      <div style="overflow-x:auto">
        <table>
          <thead>
            <tr>
              <th>Phim</th>
              <th>Thể loại</th>
              <th>Năm</th>
              <th>Đánh giá</th>
              <th>Lượt xem</th>
              <th>Trạng thái</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @forelse($movies as $movie)
              <tr>
                <td>
                  <div class="movie-cell">
                    <!-- Sử dụng cột poster đúng chuẩn DB -->
                    <img src="{{ $movie->poster ? asset('storage/' . $movie->poster) : asset('images/default-poster.png') }}" alt="{{ $movie->title }}">
                    <span class="mt">{{ $movie->title }}<small>{{ $movie->slug }}</small></span>
                  </div>
                </td>
                <td>
                  <span class="tag">
                    {{ $movie->genres->pluck('name')->implode(', ') ?: 'Chưa phân loại' }}
                  </span>
                </td>
                <td>{{ $movie->release_year }}</td>
                <!-- Lấy trung bình cộng từ bảng ratings -->
                <td class="rating">
                  {{ $movie->ratings_avg_rating ? number_format($movie->ratings_avg_rating, 1) : 'N/A' }}
                </td>
                <!-- Đếm số lượng bản ghi từ bảng movie_views -->
                <td>{{ number_format($movie->views_count) }}</td>
                <td>
                  <!-- Hiển thị dựa trên cột is_published -->
                  @if($movie->is_published)
                    <span class="status">Hiển thị</span>
                  @else
                    <span class="status off">Ẩn</span>
                  @endif
                </td>
                <td>
                  <div class="row-actions">
                    <a href="{{ route('admin.movies.edit', $movie->id) }}" class="mini" title="Sửa">✎</a>
                    
                    <!-- Bật/Tắt trạng thái is_published -->
                    <form action="{{ route('admin.movies.toggle-status', $movie->id) }}" method="POST" style="display:inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="mini" title="Đổi trạng thái (Ẩn/Hiện)">◑</button>
                    </form>

                    <form action="{{ route('admin.movies.destroy', $movie->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Xóa phim này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="mini" title="Xóa">✕</button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" style="text-align: center; padding: 20px;">Không tìm thấy phim nào.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- Phân trang -->
    @if($movies->hasPages())
        <div class="pagination">
            {{ $movies->appends(request()->query())->links('pagination::custom') }}
        </div>
    @endif
</section>
@endsection
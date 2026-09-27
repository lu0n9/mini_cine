@extends('admin.layouts.master')

@section('content')

<section id="ratings" class="page">

    <div class="page-head">
        <div>
            <h3>Ratings</h3>
            <p>
                Quản lý điểm đánh giá của người dùng cho từng phim.
            </p>
        </div>
    </div>

    <div class="panel">

        <table>

            <thead>
                <tr>
                    <th>User</th>
                    <th>Phim</th>
                    <th>Điểm</th>
                    <th>Thời gian</th>
                    <th>Trạng thái</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>

                @forelse($ratings as $rating)

                    <tr>

                        {{-- USER --}}
                        <td>
                            @if($rating->user)
                                {{ '@' . ($rating->user->game_name ?? $rating->user->name) }}
                            @else
                                <span>Unknown User</span>
                            @endif
                        </td>

                        {{-- MOVIE --}}
                        <td>
                            @if($rating->movie)
                                {{ $rating->movie->title }}
                            @else
                                <span>Phim đã bị xóa</span>
                            @endif
                        </td>

                        {{-- RATING --}}
                        <td class="rating">
                            {{ number_format($rating->rating, 1) }}
                        </td>

                        {{-- TIME --}}
                        <td>
                            {{ $rating->created_at?->diffForHumans() ?? '-' }}
                        </td>

                        {{-- STATUS --}}
                        <td>
                            <span class="status">
                                Hiển thị
                            </span>
                        </td>

                        {{-- ACTIONS --}}
                        <td>
                            <div class="row-actions">

                                <form
                                    action="{{ route('admin.ratings.destroy', $rating) }}"
                                    method="POST"
                                    style="display:inline;"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="mini"
                                        title="Xóa đánh giá"
                                        onclick="return confirm(
                                            'Bạn có chắc muốn xóa đánh giá này?'
                                        )"
                                    >
                                        ✕
                                    </button>

                                </form>

                            </div>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td
                            colspan="6"
                            style="text-align:center;padding:30px;"
                        >
                            Chưa có đánh giá nào.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

        {{-- PAGINATION --}}
        @if($ratings->hasPages())
            <div class="pagination">
                {{ $ratings->links('pagination::custom') }}
            </div>
        @endif

    </div>

</section>

@endsection
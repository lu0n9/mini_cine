@extends('admin.layouts.master')

@section('content')

<section id="comments" class="page">

    <div class="page-head">

        <div>
            <h3>Bình luận</h3>

            <p>
                Kiểm duyệt và quản lý bình luận của người dùng.
            </p>
        </div>

    </div>


    {{-- =========================
        FILTER
    ========================== --}}
    <div class="table-tools">

        {{-- Tất cả --}}
        <a
            href="{{ route('admin.comments.index', ['filter' => 'all']) }}"
            class="filter {{ $filter === 'all' ? 'on' : '' }}"
        >
            Tất cả
            <span>{{ $counts['all'] }}</span>
        </a>

        {{-- Chờ duyệt --}}
        <a
            href="{{ route('admin.comments.index', ['filter' => 'pending']) }}"
            class="filter {{ $filter === 'pending' ? 'on' : '' }}"
        >
            Chờ duyệt
            <span>{{ $counts['pending'] }}</span>
        </a>

        {{-- AI cần xem xét --}}
        <a
            href="{{ route('admin.comments.index', ['filter' => 'ai_review']) }}"
            class="filter {{ $filter === 'ai_review' ? 'on' : '' }}"
        >
            AI cần xem xét
            <span>{{ $counts['ai_review'] }}</span>
        </a>

        {{-- Đã duyệt --}}
        <a
            href="{{ route('admin.comments.index', ['filter' => 'approved']) }}"
            class="filter {{ $filter === 'approved' ? 'on' : '' }}"
        >
            Đã duyệt
            <span>{{ $counts['approved'] }}</span>
        </a>

        {{-- Spam --}}
        <a
            href="{{ route('admin.comments.index', ['filter' => 'spam']) }}"
            class="filter {{ $filter === 'spam' ? 'on' : '' }}"
        >
            Spam
            <span>{{ $counts['spam'] }}</span>
        </a>

        {{-- Đã ẩn --}}
        <a
            href="{{ route('admin.comments.index', ['filter' => 'hidden']) }}"
            class="filter {{ $filter === 'hidden' ? 'on' : '' }}"
        >
            Đã ẩn
            <span>{{ $counts['hidden'] }}</span>
        </a>

    </div>


    {{-- =========================
        TABLE
    ========================== --}}

    <div class="panel">

        <table>

            <thead>

                <tr>

                    <th>Người dùng</th>

                    <th>Nội dung</th>

                    <th>Phim</th>

                    <th>Thời gian</th>

                    <th>Trạng thái</th>

                    <th></th>

                </tr>

            </thead>


            <tbody>

                @forelse($comments as $comment)

                    <tr>

                        {{-- USER --}}
                        <td>

                            <div class="comment-user">

                                <strong>
                                    <a href="{{route('admin.users.show',$comment->user)}}">{{ $comment->user?->name ?? 'Người dùng đã xóa' }}</a>
                                </strong>

                                @if($comment->user?->email)
                                    <small>
                                        {{ $comment->user->email }}
                                    </small>
                                @endif

                            </div>

                        </td>


                        {{-- CONTENT --}}
                        <td>

                            <div class="comment-content">

                                {{ \Illuminate\Support\Str::limit(
                                    $comment->content,
                                    100
                                ) }}

                            </div>

                        </td>


                        {{-- MOVIE --}}
                        <td>

                            @if($comment->movie)

                                <a
                                    href="{{ route('admin.movies.edit', $comment->movie->id) }}"
                                    class="movie-link"
                                >
                                    {{ $comment->movie->title }}
                                </a>

                            @else

                                <span class="muted">
                                    Phim đã xóa
                                </span>

                            @endif

                        </td>


                        {{-- TIME --}}
                        <td>

                            <span
                                title="{{ $comment->created_at?->format('d/m/Y H:i:s') }}"
                            >
                                {{ $comment->created_at?->diffForHumans() }}
                            </span>

                        </td>


                        {{-- STATUS --}}
                        <td>

                            @switch($comment->status)

                                @case('approved')
                                    <span class="status">
                                        Hiển thị
                                    </span>
                                    @break

                                @case('pending')
                                    <span class="status warn">
                                        Chờ duyệt
                                    </span>
                                    @break

                                @case('spam')
                                    <span class="status off">
                                        Spam
                                    </span>
                                    @break

                                @case('hidden')
                                    <span class="status off">
                                        Đã ẩn
                                    </span>
                                    @break

                                @default
                                    <span class="status off">
                                        Không xác định
                                    </span>

                            @endswitch

                        </td>


                        {{-- ACTIONS --}}
                        <td>

                          <div class="row-actions">

                              {{-- CHỜ DUYỆT --}}
                              @if($comment->status === 'pending')

                                  <form
                                      method="POST"
                                      action="{{ route(
                                          'admin.comments.approve',
                                          $comment
                                      ) }}"
                                  >
                                      @csrf

                                      <button
                                          type="submit"
                                          class="mini"
                                          title="Duyệt"
                                      >
                                          ✓
                                      </button>
                                  </form>

                                  <form
                                      method="POST"
                                      action="{{ route(
                                          'admin.comments.spam',
                                          $comment
                                      ) }}"
                                  >
                                      @csrf

                                      <button
                                          type="submit"
                                          class="mini"
                                          title="Đánh dấu spam"
                                      >
                                          ⚑
                                      </button>
                                  </form>

                              {{-- ĐANG HIỂN THỊ --}}
                              @elseif($comment->status === 'approved')

                                  <form
                                      method="POST"
                                      action="{{ route(
                                          'admin.comments.hide',
                                          $comment
                                      ) }}"
                                  >
                                      @csrf

                                      <button
                                          type="submit"
                                          class="mini"
                                          title="Ẩn bình luận"
                                      >
                                          ◑
                                      </button>
                                  </form>

                                  <form
                                      method="POST"
                                      action="{{ route(
                                          'admin.comments.spam',
                                          $comment
                                      ) }}"
                                  >
                                      @csrf

                                      <button
                                          type="submit"
                                          class="mini"
                                          title="Đánh dấu spam"
                                      >
                                          ⚑
                                      </button>
                                  </form>

                              {{-- SPAM --}}
                              @elseif($comment->status === 'spam')

                                  <form
                                      method="POST"
                                      action="{{ route(
                                          'admin.comments.approve',
                                          $comment
                                      ) }}"
                                  >
                                      @csrf

                                      <button
                                          type="submit"
                                          class="mini"
                                          title="Khôi phục"
                                      >
                                          ↻
                                      </button>
                                  </form>

                              {{-- HIDDEN --}}
                              @elseif($comment->status === 'hidden')

                                  <form
                                      method="POST"
                                      action="{{ route(
                                          'admin.comments.show',
                                          $comment
                                      ) }}"
                                  >
                                      @csrf

                                      <button
                                          type="submit"
                                          class="mini"
                                          title="Hiển thị lại"
                                      >
                                          ◉
                                      </button>
                                  </form>

                              @endif


                              {{-- DELETE --}}
                              <form
                                  method="POST"
                                  action="{{ route(
                                      'admin.comments.destroy',
                                      $comment
                                  ) }}"
                                  onsubmit="return confirm(
                                      'Bạn có chắc muốn xóa bình luận này?'
                                  )"
                              >

                                  @csrf
                                  @method('DELETE')

                                  <button
                                      type="submit"
                                      class="mini"
                                      title="Xóa"
                                  >
                                      ×
                                  </button>

                              </form>

                          </div>

                      </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="6"
                            style="text-align:center;padding:40px;"
                        >
                            Không có bình luận nào.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- =========================
        PAGINATION
    ========================== --}}

    @if($comments->hasPages())

        <div class="pagination">
            {{ $comments->links('pagination::custom') }}
        </div>

    @endif

</section>

@endsection
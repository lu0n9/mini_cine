@extends('admin.layouts.master')

@section('content')

<section id="reports" class="page">

    <div class="page-head">

        <div>
            <h3>Reports / Báo lỗi</h3>

            <p>
                Xử lý báo lỗi từ người xem:
                link chết, mất tiếng, sai phụ đề...
            </p>
        </div>

    </div>


    {{-- FILTER --}}

    <div class="table-tools">

        <a
            href="{{ route('admin.reports.index', ['filter' => 'pending']) }}"
            class="filter {{ $filter === 'pending' ? 'on' : '' }}"
        >
            Pending
            <span>{{ $counts['pending'] }}</span>
        </a>

        <a
            href="{{ route('admin.reports.index', ['filter' => 'processing']) }}"
            class="filter {{ $filter === 'processing' ? 'on' : '' }}"
        >
            Processing
            <span>{{ $counts['processing'] }}</span>
        </a>

        <a
            href="{{ route('admin.reports.index', ['filter' => 'resolved']) }}"
            class="filter {{ $filter === 'resolved' ? 'on' : '' }}"
        >
            Resolved
            <span>{{ $counts['resolved'] }}</span>
        </a>

        <a
            href="{{ route('admin.reports.index', ['filter' => 'rejected']) }}"
            class="filter {{ $filter === 'rejected' ? 'on' : '' }}"
        >
            Rejected
            <span>{{ $counts['rejected'] }}</span>
        </a>

    </div>


    {{-- TABLE --}}

    <div class="panel">

        <table>
            <thead>
                <tr>
                    <th>Loại lỗi</th>
                    <th>Phim / Tập</th>
                    <th>User</th>
                    <th>Nội dung lỗi</th>
                    <th>Thời gian</th>
                    <th>Trạng thái</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>

                @forelse($reports as $report)

                    <tr>

                        {{-- TYPE --}}

                        <td>
                            @switch($report->type)

                                @case('video')
                                    Video không phát được
                                    @break

                                @case('audio')
                                    Không có tiếng
                                    @break

                                @case('subtitle')
                                    Sai / mất phụ đề
                                    @break

                                @case('wrong_episode')
                                    Sai tập phim
                                    @break

                                @case('buffering')
                                    Video bị giật / buffering
                                    @break

                                @case('quality')
                                    Chất lượng video thấp
                                    @break

                                @default
                                    Lỗi khác

                            @endswitch
                        </td>


                        {{-- MOVIE / EPISODE --}}

                        <td>

                            @if($report->movie)

                                {{ $report->movie->title }}

                                @if($report->episode)

                                    <span>
                                        · Tập
                                        {{ $report->episode->episode_number }}
                                    </span>

                                @endif

                            @else

                                <span>Phim đã bị xóa</span>

                            @endif

                        </td>


                        {{-- USER --}}

                        <td>

                            @if($report->user)

                                {{ '@' . ($report->user->game_name ?? $report->user->name) }}

                            @else

                                Guest

                            @endif

                        </td>

                        <td>
                            <div
                                style="
                                    max-width: 320px;
                                    white-space: normal;
                                    line-height: 1.5;
                                "
                                title="{{ $report->message }}"
                            >
                                {{ $report->message }}
                            </div>
                        </td>

                        {{-- TIME --}}

                        <td>

                            {{ $report->created_at?->diffForHumans() ?? '-' }}

                        </td>


                        {{-- STATUS --}}

                        <td>

                            @switch($report->status)

                                @case('pending')

                                    <span class="status warn">
                                        Pending
                                    </span>

                                    @break

                                @case('processing')

                                    <span class="status warn">
                                        Processing
                                    </span>

                                    @break

                                @case('resolved')

                                    <span class="status">
                                        Resolved
                                    </span>

                                    @break

                                @case('rejected')

                                    <span class="status">
                                        Rejected
                                    </span>

                                    @break

                                @default

                                    <span class="status">
                                        {{ $report->status }}
                                    </span>

                            @endswitch

                        </td>


                        {{-- ACTIONS --}}

                        <td>

                            <div class="row-actions">

                                @if(
                                    in_array(
                                        $report->status,
                                        ['pending', 'processing']
                                    )
                                )

                                    {{-- Resolve --}}

                                    <form
                                        action="{{ route(
                                            'admin.reports.update-status',
                                            $report
                                        ) }}"
                                        method="POST"
                                        style="display:inline;"
                                    >

                                        @csrf
                                        @method('PATCH')

                                        <input
                                            type="hidden"
                                            name="status"
                                            value="resolved"
                                        >

                                        <button
                                            type="submit"
                                            class="mini"
                                            title="Đã xử lý"
                                        >
                                            ✔
                                        </button>

                                    </form>


                                    {{-- Reject --}}

                                    <form
                                        action="{{ route(
                                            'admin.reports.update-status',
                                            $report
                                        ) }}"
                                        method="POST"
                                        style="display:inline;"
                                    >

                                        @csrf
                                        @method('PATCH')

                                        <input
                                            type="hidden"
                                            name="status"
                                            value="rejected"
                                        >

                                        <button
                                            type="submit"
                                            class="mini"
                                            title="Từ chối"
                                        >
                                            ✕
                                        </button>

                                    </form>

                                @endif


                                {{-- Processing --}}

                                @if($report->status === 'pending')

                                    <form
                                        action="{{ route(
                                            'admin.reports.update-status',
                                            $report
                                        ) }}"
                                        method="POST"
                                        style="display:inline;"
                                    >

                                        @csrf
                                        @method('PATCH')

                                        <input
                                            type="hidden"
                                            name="status"
                                            value="processing"
                                        >

                                        <button
                                            type="submit"
                                            class="mini"
                                            title="Đang xử lý"
                                        >
                                            ⚙
                                        </button>

                                    </form>

                                @endif


                                {{-- DELETE --}}

                                <form
                                    action="{{ route(
                                        'admin.reports.destroy',
                                        $report
                                    ) }}"
                                    method="POST"
                                    style="display:inline;"
                                    onsubmit="return confirm(
                                        'Bạn có chắc muốn xóa báo lỗi này?'
                                    )"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="mini"
                                        title="Xóa"
                                    >
                                        🗑
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>


                    {{-- REPORT CONTENT --}}

                    <tr>

                        <td
                            colspan="6"
                            style="
                                padding-top: 0;
                                color: #777;
                                font-size: 13px;
                            "
                        >

                            {{ $report->content }}

                        </td>

                    </tr>


                @empty

                    <tr>

                        <td
                            colspan="6"
                            style="
                                text-align:center;
                                padding:40px;
                                color:#777;
                            "
                        >
                            Không có báo lỗi nào.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>


        {{-- PAGINATION --}}

        @if($reports->hasPages())

            <div style="margin-top:20px;">
                {{ $reports->links('pagination::custom') }}
            </div>

        @endif

    </div>

</section>

@endsection
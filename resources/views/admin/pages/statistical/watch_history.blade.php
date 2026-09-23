@extends('admin.layouts.master')

@section('content')

<section id="history" class="page">

    {{-- HEADER --}}
    <div class="page-head">
        <div>
            <h3>Watch History</h3>
            <p>Lịch sử xem và tiến độ của người dùng.</p>
        </div>
    </div>


    {{-- STATS --}}
    <div class="stats">

        <div class="stat">
            <span class="label">Phim xem dở</span>
            <div class="num">
                {{ number_format($unfinishedCount) }}
            </div>
        </div>

        <div class="stat">
            <span class="label">Đã xem xong</span>
            <div class="num">
                {{ number_format($completedCount) }}
            </div>
        </div>

        <div class="stat">
            <span class="label">Tiến độ TB</span>
            <div class="num">
                {{ $averageProgress }}%
            </div>
        </div>

        <div class="stat">
            <span class="label">Completion rate</span>
            <div class="num">
                {{ $completionRate }}%
            </div>
        </div>

    </div>


    {{-- WATCH HISTORY TABLE --}}
    <div class="panel">

        <table>

            <thead>
                <tr>
                    <th>User</th>
                    <th>Phim / Tập</th>
                    <th>Tiến độ</th>
                    <th>Xem lần cuối</th>
                    <th>Hoàn thành</th>
                </tr>
            </thead>

            <tbody>

                @forelse($watchHistories as $history)

                    @php

                        $watchTime = (int) ($history->watch_time ?? 0);

                        /*
                         * Ưu tiên duration của watch_history.
                         * Nếu không có thì lấy duration của episode.
                         */
                        $duration = (int) (
                            $history->duration
                            ?? $history->episode?->duration
                            ?? 0
                        );

                        $progress = 0;

                        if ($duration > 0) {
                            $progress = round(
                                min(
                                    ($watchTime / $duration) * 100,
                                    100
                                )
                            );
                        }

                        $isCompleted =
                            $duration > 0 &&
                            $watchTime >= $duration;

                    @endphp

                    <tr>

                        {{-- USER --}}
                        <td>
                            @if($history->user)

                                {{ '@' . ($history->user->name ?? 'user') }}

                            @else

                                Guest

                            @endif
                        </td>


                        {{-- MOVIE / EPISODE --}}
                        <td>

                            @if($history->movie)

                                <div>
                                    {{ $history->movie->title }}
                                </div>

                                @if($history->episode)

                                    <small style="color:#888;">
                                        @if($history->episode->season)
                                            Mùa {{ $history->episode->season->season_number }}
                                        @endif

                                        ·

                                        Tập {{ $history->episode->episode_number }}

                                        @if($history->episode->name)
                                            · {{ $history->episode->name }}
                                        @endif
                                    </small>

                                @endif

                            @else

                                <span style="color:#999;">
                                    Phim không tồn tại
                                </span>

                            @endif

                        </td>


                        {{-- PROGRESS --}}
                        <td style="width:180px;">

                            <div
                                style="
                                    display:flex;
                                    align-items:center;
                                    gap:8px;
                                "
                            >

                                <div
                                    class="prog"
                                    style="
                                        width:120px;
                                        flex-shrink:0;
                                    "
                                >
                                    <i
                                        style="
                                            width:{{ $progress }}%;
                                        "
                                    ></i>
                                </div>

                                <span
                                    style="
                                        font-size:12px;
                                        color:#666;
                                        min-width:35px;
                                    "
                                >
                                    {{ $progress }}%
                                </span>

                            </div>

                        </td>


                        {{-- LAST WATCHED --}}
                        <td>

                            @if($history->last_watched_at)

                                {{ $history->last_watched_at->diffForHumans() }}

                            @else

                                {{ $history->updated_at?->diffForHumans() ?? '—' }}

                            @endif

                        </td>


                        {{-- COMPLETED --}}
                        <td>

                            @if($isCompleted)

                                <span
                                    style="
                                        font-weight:600;
                                        color:#111;
                                    "
                                >
                                    ✔
                                </span>

                            @else

                                <span style="color:#999;">
                                    —
                                </span>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="5"
                            style="
                                text-align:center;
                                padding:40px 20px;
                                color:#888;
                            "
                        >
                            Chưa có lịch sử xem.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- PAGINATION --}}
    @if($watchHistories->hasPages())

        <div style="margin-top:20px;">
            {{ $watchHistories->links('pagination::custom') }}
        </div>

    @endif

</section>

@endsection
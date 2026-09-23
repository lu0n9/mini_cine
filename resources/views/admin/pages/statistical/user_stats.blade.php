@extends('admin.layouts.master')

@section('content')

<section id="user-stats" class="page">

    {{-- HEADER --}}
    <div class="page-head">
        <div>
            <h3>User Statistics</h3>
            <p>Tăng trưởng và phân bố người dùng.</p>
        </div>
    </div>


    {{-- STATISTICS --}}
    <div class="stats">

        <div class="stat">
            <span class="label">Tổng user</span>

            <div class="num">
                {{ number_format($totalUsers) }}
            </div>
        </div>


        <div class="stat">
            <span class="label">User mới (tháng)</span>

            <div class="num">
                {{ number_format($newUsersThisMonth) }}
            </div>
        </div>


        <div class="stat">
            <span class="label">Premium</span>

            <div class="num">
                {{ number_format($premiumUsers) }}
            </div>
        </div>


        <div class="stat">
            <span class="label">Hoạt động/ngày</span>

            <div class="num">
                {{ number_format($activeUsersToday) }}
            </div>
        </div>

    </div>


    {{-- MONTHLY USERS --}}
    <div class="panel">

        <div class="panel-head">
            <h4>Người dùng mới theo tháng</h4>
        </div>

        <div class="panel-body">

            @if($monthlyChart->count())

                <div class="chart">

                    @foreach($monthlyChart as $item)

                        <div
                            class="bar-wrap"
                            title="{{ $item['total'] }} user mới"
                        >

                            <div
                                class="bar"
                                style="height: {{ $item['height'] }}%;"
                            ></div>

                            <span class="bar-x">
                                {{ $item['label'] }}
                            </span>

                        </div>

                    @endforeach

                </div>

            @else

                <div style="
                    text-align:center;
                    padding:40px 20px;
                    color:#888;
                ">
                    Chưa có dữ liệu người dùng.
                </div>

            @endif

        </div>

    </div>

</section>

@endsection
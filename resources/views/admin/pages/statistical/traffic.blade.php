@extends('admin.layouts.master')

@section('content')

<section id="traffic" class="page">

    <div class="page-head">
        <div>
            <h3>Traffic</h3>
            <p>Nguồn truy cập theo thiết bị, trình duyệt và quốc gia.</p>
        </div>
    </div>


    <div class="grid-3">

        {{-- =========================
             THIẾT BỊ
        ========================== --}}

        <div class="panel">

            <div class="panel-head">
                <h4>Thiết bị</h4>
            </div>

            <div class="panel-body activity">

                @forelse($devices as $device)

                    <div class="act">

                        <div class="txt">

                            <b>
                                {{ $device['name'] }}
                            </b>

                            — {{ $device['percentage'] }}%

                            <small>

                                <div
                                    class="prog"
                                    style="margin-top:6px"
                                >
                                    <i
                                        style="
                                            width:{{ $device['percentage'] }}%
                                        "
                                    ></i>
                                </div>

                            </small>

                        </div>

                    </div>

                @empty

                    <div style="
                        text-align:center;
                        padding:20px;
                        color:#888;
                    ">
                        Chưa có dữ liệu.
                    </div>

                @endforelse

            </div>

        </div>


        {{-- =========================
             TRÌNH DUYỆT
        ========================== --}}

        <div class="panel">

            <div class="panel-head">
                <h4>Trình duyệt</h4>
            </div>

            <div class="panel-body activity">

                @forelse($browsers as $browser)

                    <div class="act">

                        <div class="txt">

                            <b>
                                {{ $browser['name'] }}
                            </b>

                            — {{ $browser['percentage'] }}%

                        </div>

                    </div>

                @empty

                    <div style="
                        text-align:center;
                        padding:20px;
                        color:#888;
                    ">
                        Chưa có dữ liệu.
                    </div>

                @endforelse

            </div>

        </div>


        {{-- =========================
             QUỐC GIA
        ========================== --}}

        <div class="panel">

            <div class="panel-head">
                <h4>Quốc gia</h4>
            </div>

            <div class="panel-body activity">

                @forelse($countries as $country)

                    <div class="act">

                        <div class="txt">

                            <b>
                                {{ $country['name'] }}
                            </b>

                            — {{ $country['percentage'] }}%

                        </div>

                    </div>

                @empty

                    <div style="
                        text-align:center;
                        padding:20px;
                        color:#888;
                    ">
                        Chưa có dữ liệu.
                    </div>

                @endforelse

            </div>

        </div>

    </div>

</section>

@endsection
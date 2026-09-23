@extends('admin.layouts.master')

@section('content')

<section id="collections" class="page">

    <div class="page-head">

        <div>
            <h3>Collections</h3>

            <p>
                Nhóm phim theo vũ trụ / series như Marvel, DC, Fast &amp; Furious.
            </p>
        </div>

        <a
            href="{{route('admin.collections.create')}}"
            class="btn"
        >
            + Thêm collection
        </a>

    </div>


    @if(session('success'))
        <div class="alert success">
            {{ session('success') }}
        </div>
    @endif


    @if(session('error'))
        <div class="alert error">
            {{ session('error') }}
        </div>
    @endif


    <div class="grid-3">

        @forelse($collections as $collection)

            <div class="panel">

                <div class="panel-body">

                    <h4 style="margin-bottom:6px">
                        {{ $collection->name }}
                    </h4>


                    @if($collection->description)

                        <p class="hint">
                            {{ $collection->description }}
                        </p>

                    @endif


                    <p class="hint">

                        {{ $collection->movies_count }} phim

                        ·

                        {{ number_format($collection->total_views ?? 0) }}
                        lượt xem

                    </p>


                    <div
                        style="
                            margin-top:12px;
                            display:flex;
                            gap:8px;
                            flex-wrap:wrap;
                        "
                    >

                        {{-- Xem --}}
                        <a
                            href="{{ route('admin.collections.show', $collection) }}"
                            class="btn ghost sm"
                        >
                            👁 Xem
                        </a>


                        {{-- Sửa --}}
                        <a
                            href="{{ route('admin.collections.edit', $collection) }}"
                            class="btn ghost sm"
                        >
                            ✎ Sửa
                        </a>


                        {{-- Xóa --}}
                        <form
                            action="{{ route('admin.collections.destroy', $collection) }}"
                            method="POST"
                            onsubmit="return confirm('Bạn có chắc muốn xóa Collection này? Các phim sẽ không bị xóa.')"
                            style="display:inline;"
                        >

                            @csrf

                            @method('DELETE')

                            <button
                                type="submit"
                                class="btn ghost sm"
                            >
                                🗑 Xóa
                            </button>

                        </form>

                    </div>

                </div>

            </div>

        @empty

            <div
                class="panel"
                style="grid-column:1/-1;"
            >

                <div class="panel-body">

                    <p>
                        Chưa có Collection nào.
                    </p>

                </div>

            </div>

        @endforelse

    </div>

</section>

@endsection
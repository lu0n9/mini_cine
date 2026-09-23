@extends('admin.layouts.master')

@section('content')

<section id="tags" class="page">

    <div class="page-head">

        <div>
            <h3>Tags</h3>

            <p>
                Quản lý thẻ gắn cho phim.
            </p>
        </div>

        <a
            href="{{ route('admin.tags.create') }}"
            class="btn"
        >
            + Thêm tag
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


    <div class="panel">

        <div
            class="panel-body"
            style="display:flex;flex-wrap:wrap;gap:10px"
        >

            @forelse($tags as $tag)

                <div
                    style="
                        display:flex;
                        align-items:center;
                        gap:6px;
                    "
                >

                    <a
                        href="{{ route('admin.tags.edit', $tag) }}"
                        class="tag"
                    >
                        {{ $tag->name }}

                        <b style="margin-left:6px">
                            {{ $tag->movies_count }}
                        </b>
                    </a>


                    <form
                        action="{{ route('admin.tags.destroy', $tag) }}"
                        method="POST"
                        onsubmit="return confirm('Bạn có chắc muốn xóa Tag này?')"
                    >

                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="mini"
                            title="Xóa Tag"
                        >
                            ✕
                        </button>

                    </form>

                </div>

            @empty

                <p>
                    Chưa có Tag nào.
                </p>

            @endforelse

        </div>

    </div>


    <div style="margin-top:20px;">
        {{ $tags->links() }}
    </div>

</section>

@endsection
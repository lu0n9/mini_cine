@extends('admin.layouts.master')

@section('content')

<section id="people" class="page">

    {{-- Header --}}
    <div class="page-head">

        <div>
            <h3>Diễn viên - Đạo diễn</h3>

            <p>
                Quản lý hồ sơ diễn viên, đạo diễn và thông tin cá nhân.
            </p>
        </div>

        <a
            href="{{ route('admin.people.create') }}"
            class="btn"
        >
            + Thêm
        </a>

    </div>


    {{-- Success --}}
    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- Error --}}
    @if(session('error'))

        <div class="alert alert-danger">
            {{ session('error') }}
        </div>

    @endif


    {{-- People list --}}
    <div class="panel">

        <table>

            <thead>

                <tr>
                    <th>Diễn viên / Đạo diễn</th>
                    <th>Slug</th>
                    <th>Số phim</th>
                    <th></th>
                </tr>

            </thead>


            <tbody>

                @forelse($people as $person)

                    <tr>

                        {{-- Person --}}
                        <td>

                            <div class="movie-cell sm">

                                @if($person->avatar)

                                    <img
                                        src="{{ asset('storage/' . $person->avatar) }}"
                                        alt="{{ $person->name }}"
                                    >

                                @else

                                    <div class="avatar-placeholder">
                                        {{ strtoupper(substr($person->name, 0, 1)) }}
                                    </div>

                                @endif


                                <span class="mt">
                                    {{ $person->name }}
                                </span>

                            </div>

                        </td>


                        {{-- Slug --}}
                        <td>
                            {{ $person->slug }}
                        </td>


                        {{-- Movie count --}}
                        <td>
                            {{ $person->movies_count }}
                        </td>


                        {{-- Actions --}}
                        <td>

                            <div class="row-actions">

                                {{-- Edit --}}
                                <a
                                    href="{{ route('admin.people.edit', $person->id) }}"
                                    class="mini"
                                    title="Sửa"
                                >
                                    ✎
                                </a>


                                {{-- Delete --}}
                                <form
                                    action="{{ route('admin.people.destroy', $person->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Bạn có chắc muốn xóa {{ $person->name }}?')"
                                >

                                    @csrf

                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="mini"
                                        title="Xóa"
                                    >
                                        ✕
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="4">
                            Chưa có diễn viên hoặc đạo diễn nào.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    @if($people->hasPages())
        <div class="pagination">
            {{ $people->links('pagination::custom') }}
        </div>
    @endif

</section>

@endsection
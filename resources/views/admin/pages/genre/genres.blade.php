@extends('admin.layouts.master')

@section('content')

<section id="genres" class="page">

    <div class="page-head">
        <div>
            <h3>Thể loại</h3>
            <p>Quản lý thể loại phim kèm slug, mô tả và SEO.</p>
        </div>

        <a href="{{ route('admin.genres.create') }}" class="btn">
            + Thêm thể loại
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="panel">
        <table>

            <thead>
                <tr>
                    <th>Tên</th>
                    <th>Slug</th>
                    <th>Số phim</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>

                @forelse($genres as $genre)

                    <tr>

                        {{-- Tên --}}
                        <td>
                            {{ $genre->name }}
                        </td>

                        {{-- Slug --}}
                        <td>
                            {{ $genre->slug }}
                        </td>

                        {{-- Số phim --}}
                        <td>
                            {{ $genre->movies_count }}
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div class="row-actions">

                                <a
                                    href="{{ route('admin.genres.edit', $genre->id) }}"
                                    class="mini"
                                    title="Sửa"
                                >
                                    ✎
                                </a>

                                <form
                                    action="{{ route('admin.genres.destroy', $genre->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Bạn có chắc muốn xóa thể loại {{ $genre->name }}?')"
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
                            Chưa có thể loại nào.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>
    </div>

</section>

@endsection
@extends('admin.layouts.master')

@section('content')

<section id="pages" class="page">

    <div class="page-head">

        <div>
            <h3>Trang tĩnh</h3>

            <p>
                About, Contact, Privacy, Terms, DMCA, FAQ —
                publish/draft và SEO.
            </p>
        </div>

        <a
            href="{{ route('admin.pages.create') }}"
            class="btn"
        >
            + Thêm trang
        </a>

    </div>


    {{-- Thông báo --}}
    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    <div class="panel">

        <table>

            <thead>

                <tr>
                    <th>Tiêu đề</th>
                    <th>Slug</th>
                    <th>Cập nhật</th>
                    <th>Trạng thái</th>
                    <th></th>
                </tr>

            </thead>


            <tbody>

                @forelse($pages as $page)

                    <tr>

                        <td>
                            {{ $page->title }}
                        </td>


                        <td>
                            /{{ $page->slug }}
                        </td>


                        <td>
                            {{ $page->updated_at?->format('d/m/Y') ?? '—' }}
                        </td>


                        <td>

                            @if($page->status === 'published')

                                <span class="status">
                                    Published
                                </span>

                            @else

                                <span class="status off">
                                    Draft
                                </span>

                            @endif

                        </td>


                        <td>
                            <div class="row-actions">

                                {{-- Sửa --}}
                                <a
                                    href="{{ route('admin.pages.edit', $page) }}"
                                    class="mini"
                                    title="Sửa trang"
                                >
                                    ✎
                                </a>

                                {{-- Đổi trạng thái --}}
                                <form
                                    action="{{ route('admin.pages.toggle-status', $page) }}"
                                    method="POST"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="mini"
                                        title="{{ $page->status === 'published' ? 'Chuyển sang Draft' : 'Xuất bản' }}"
                                    >
                                        {{ $page->status === 'published' ? '◉' : '○' }}
                                    </button>
                                </form>

                                {{-- Xóa --}}
                                <form
                                    action="{{ route('admin.pages.destroy', $page) }}"
                                    method="POST"
                                    onsubmit="return confirm('Bạn có chắc muốn xóa trang này?')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="mini"
                                        title="Xóa trang"
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
                            colspan="5"
                            style="text-align:center;"
                        >
                            Chưa có trang nào.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- Pagination --}}

    @if($pages->hasPages())

        <div class="pagination">
            {{ $pages->links('pagination::custom') }}
        </div>

    @endif

</section>

@endsection
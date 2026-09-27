@extends('admin.layouts.master')

@section('content')

<section id="countries" class="page">

    {{-- Header --}}

    <div class="page-head">

        <div>

            <h3>Quốc gia</h3>

            <p>
                Quản lý quốc gia sản xuất kèm mã quốc gia.
            </p>

        </div>

        <a
            class="btn"
            href="{{ route('admin.countries.create') }}"
        >
            + Thêm quốc gia
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


    {{-- Country table --}}

    <div class="panel">

        <table>

            <thead>

                <tr>
                    <th>Tên</th>
                    <th>Slug</th>
                    <th>Mã</th>
                    <th>Số phim</th>
                    <th>Trạng thái</th>
                    <th></th>
                </tr>

            </thead>


            <tbody>

                @forelse($countries as $country)

                    <tr>

                        {{-- Name --}}

                        <td>
                            {{ $country->name }}
                        </td>


                        {{-- Slug --}}

                        <td>
                            {{ $country->slug }}
                        </td>


                        {{-- Code --}}

                        <td>
                            {{ strtoupper($country->code) }}
                        </td>


                        {{-- Movie count --}}

                        <td>
                            {{ $country->movies_count }}
                        </td>


                        {{-- Status --}}

                        <td>
                            <span class="status">
                                Hiển thị
                            </span>
                        </td>


                        {{-- Actions --}}

                        <td>

                            <div class="row-actions">

                                <a
                                    href="{{ route('admin.countries.edit', $country->id) }}"
                                    class="mini"
                                    title="Sửa"
                                >
                                    ✎
                                </a>


                                <form
                                    action="{{ route('admin.countries.destroy', $country->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Bạn có chắc muốn xóa quốc gia {{ $country->name }}?')"
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

                        <td colspan="6">
                            Chưa có quốc gia nào.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    @if($countries->hasPages())
        <div class="pagination">
            {{ $countries->links('pagination::custom') }}
        </div>
    @endif

</section>

@endsection
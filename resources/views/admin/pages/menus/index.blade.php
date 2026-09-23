@extends('admin.layouts.master')

@section('content')

<section id="menus" class="page">

    {{-- =========================
        HEADER
    ========================== --}}
    <div class="page-head">

        <div>
            <h3>Menus</h3>
            <p>Quản lý Main menu, Footer menu và Mobile menu.</p>
        </div>

        <a href="{{ route('admin.menus.create') }}" class="btn">
            + Thêm menu
        </a>

    </div>


    {{-- =========================
        FLASH MESSAGE
    ========================== --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif


    {{-- =========================
        VALIDATION
    ========================== --}}
    @if($errors->any())

        <div class="alert alert-error">

            <ul style="margin:0;padding-left:18px;">

                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>

    @endif


    <div class="grid-3">


        {{-- =====================================================
            MAIN MENU
        ====================================================== --}}
        <div class="panel">

            <div class="panel-head">
                <h4>Main menu</h4>
            </div>

            <div class="panel-body menu-list">

                @forelse($menusByLocation['main'] as $menu)

                    <div class="menu-row">

                        {{-- MENU CHA --}}
                        <div class="menu-main">

                            {{-- Nút xổ xuống --}}
                            @if($menu->children->count())

                                <button
                                    type="button"
                                    class="menu-toggle"
                                    aria-expanded="false"
                                    aria-label="Hiển thị menu con"
                                >
                                    <span class="menu-arrow"></span>
                                </button>

                            @else

                                <span class="menu-toggle-placeholder"></span>

                            @endif


                            <div class="menu-info">

                                <div class="menu-title">
                                    <b>{{ $menu->name }}</b>
                                </div>

                                @if($menu->url)

                                    <small class="menu-url">
                                        {{ $menu->url }}
                                    </small>

                                @elseif($menu->children->count())

                                    <small class="menu-child-count">
                                        menu con · {{ $menu->children->count() }} mục
                                    </small>

                                @endif

                            </div>


                            {{-- ACTION MENU CHA --}}
                            <div class="menu-actions">

                                @if($menu->is_active)

                                    <span class="menu-status active">
                                        Đang bật
                                    </span>

                                @else

                                    <span class="menu-status inactive">
                                        Đã tắt
                                    </span>

                                @endif


                                <a
                                    href="{{ route('admin.menus.edit', $menu) }}"
                                    class="menu-action edit"
                                >
                                    Sửa
                                </a>


                                <form
                                    action="{{ route('admin.menus.destroy', $menu) }}"
                                    method="POST"
                                    onsubmit="return confirm('Bạn có chắc muốn xóa menu này?')"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="menu-action delete"
                                    >
                                        Xóa
                                    </button>

                                </form>

                            </div>

                        </div>


                        {{-- =================================================
                            MENU CON
                        ================================================== --}}
                        @if($menu->children->count())

                            <div class="menu-children">

                                @foreach($menu->children as $child)

                                    <div class="menu-child-row">

                                        <div class="menu-child-info">

                                            <span class="child-dot"></span>

                                            <div>

                                                <span class="child-name">
                                                    {{ $child->name }}
                                                </span>

                                                @if($child->url)

                                                    <small class="child-url">
                                                        {{ $child->url }}
                                                    </small>

                                                @endif

                                            </div>

                                        </div>


                                        {{-- ACTION MENU CON --}}
                                        <div class="child-actions">

                                            <a
                                                href="{{ route('admin.menus.edit', $child) }}"
                                                class="child-action edit"
                                            >
                                                Sửa
                                            </a>

                                            <form
                                                action="{{ route('admin.menus.destroy', $child) }}"
                                                method="POST"
                                                onsubmit="return confirm('Bạn có chắc muốn xóa menu này?')"
                                            >

                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="child-action delete"
                                                >
                                                    Xóa
                                                </button>

                                            </form>

                                        </div>

                                    </div>

                                @endforeach

                            </div>

                        @endif

                    </div>

                @empty

                    <div class="menu-empty">
                        Chưa có Main menu.
                    </div>

                @endforelse

            </div>

        </div>



        {{-- =====================================================
            FOOTER MENU
        ====================================================== --}}
        <div class="panel">

            <div class="panel-head">
                <h4>Footer menu</h4>
            </div>

            <div class="panel-body menu-list">

                @forelse($menusByLocation['footer'] as $menu)

                    <div class="menu-row">

                        <div class="menu-main">

                            @if($menu->children->count())

                                <button
                                    type="button"
                                    class="menu-toggle"
                                    aria-expanded="false"
                                    aria-label="Hiển thị menu con"
                                >
                                    <span class="menu-arrow"></span>
                                </button>

                            @else

                                <span class="menu-toggle-placeholder"></span>

                            @endif


                            <div class="menu-info">

                                <div class="menu-title">
                                    <b>{{ $menu->name }}</b>
                                </div>

                                @if($menu->url)

                                    <small class="menu-url">
                                        {{ $menu->url }}
                                    </small>

                                @elseif($menu->children->count())

                                    <small class="menu-child-count">
                                        menu con · {{ $menu->children->count() }} mục
                                    </small>

                                @endif

                            </div>


                            <div class="menu-actions">

                                <a
                                    href="{{ route('admin.menus.edit', $menu) }}"
                                    class="menu-action edit"
                                >
                                    Sửa
                                </a>

                                <form
                                    action="{{ route('admin.menus.destroy', $menu) }}"
                                    method="POST"
                                    onsubmit="return confirm('Bạn có chắc muốn xóa menu này?')"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="menu-action delete"
                                    >
                                        Xóa
                                    </button>

                                </form>

                            </div>

                        </div>


                        @if($menu->children->count())

                            <div class="menu-children">

                                @foreach($menu->children as $child)

                                    <div class="menu-child-row">

                                        <div class="menu-child-info">

                                            <span class="child-dot"></span>

                                            <div>

                                                <span class="child-name">
                                                    {{ $child->name }}
                                                </span>

                                                @if($child->url)

                                                    <small class="child-url">
                                                        {{ $child->url }}
                                                    </small>

                                                @endif

                                            </div>

                                        </div>


                                        <div class="child-actions">

                                            <a
                                                href="{{ route('admin.menus.edit', $child) }}"
                                                class="child-action edit"
                                            >
                                                Sửa
                                            </a>

                                            <form
                                                action="{{ route('admin.menus.destroy', $child) }}"
                                                method="POST"
                                                onsubmit="return confirm('Bạn có chắc muốn xóa menu này?')"
                                            >

                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="child-action delete"
                                                >
                                                    Xóa
                                                </button>

                                            </form>

                                        </div>

                                    </div>

                                @endforeach

                            </div>

                        @endif

                    </div>

                @empty

                    <div class="menu-empty">
                        Chưa có Footer menu.
                    </div>

                @endforelse

            </div>

        </div>



        {{-- =====================================================
            MOBILE MENU
        ====================================================== --}}
        <div class="panel">

            <div class="panel-head">
                <h4>Mobile menu</h4>
            </div>

            <div class="panel-body menu-list">

                @forelse($menusByLocation['mobile'] as $menu)

                    <div class="menu-row">

                        <div class="menu-main">

                            @if($menu->children->count())

                                <button
                                    type="button"
                                    class="menu-toggle"
                                    aria-expanded="false"
                                    aria-label="Hiển thị menu con"
                                >
                                    <span class="menu-arrow"></span>
                                </button>

                            @else

                                <span class="menu-toggle-placeholder"></span>

                            @endif


                            <div class="menu-info">

                                <div class="menu-title">
                                    <b>{{ $menu->name }}</b>
                                </div>

                                @if($menu->url)

                                    <small class="menu-url">
                                        {{ $menu->url }}
                                    </small>

                                @elseif($menu->children->count())

                                    <small class="menu-child-count">
                                        menu con · {{ $menu->children->count() }} mục
                                    </small>

                                @endif

                            </div>


                            <div class="menu-actions">

                                <a
                                    href="{{ route('admin.menus.edit', $menu) }}"
                                    class="menu-action edit"
                                >
                                    Sửa
                                </a>

                                <form
                                    action="{{ route('admin.menus.destroy', $menu) }}"
                                    method="POST"
                                    onsubmit="return confirm('Bạn có chắc muốn xóa menu này?')"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="menu-action delete"
                                    >
                                        Xóa
                                    </button>

                                </form>

                            </div>

                        </div>


                        @if($menu->children->count())

                            <div class="menu-children">

                                @foreach($menu->children as $child)

                                    <div class="menu-child-row">

                                        <div class="menu-child-info">

                                            <span class="child-dot"></span>

                                            <div>

                                                <span class="child-name">
                                                    {{ $child->name }}
                                                </span>

                                                @if($child->url)

                                                    <small class="child-url">
                                                        {{ $child->url }}
                                                    </small>

                                                @endif

                                            </div>

                                        </div>


                                        <div class="child-actions">

                                            <a
                                                href="{{ route('admin.menus.edit', $child) }}"
                                                class="child-action edit"
                                            >
                                                Sửa
                                            </a>

                                            <form
                                                action="{{ route('admin.menus.destroy', $child) }}"
                                                method="POST"
                                                onsubmit="return confirm('Bạn có chắc muốn xóa menu này?')"
                                            >

                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="child-action delete"
                                                >
                                                    Xóa
                                                </button>

                                            </form>

                                        </div>

                                    </div>

                                @endforeach

                            </div>

                        @endif

                    </div>

                @empty

                    <div class="menu-empty">
                        Chưa có Mobile menu.
                    </div>

                @endforelse

            </div>

        </div>

    </div>

</section>

@endsection
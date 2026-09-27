@extends('admin.layouts.master')

@section('title', 'Sửa danh mục Forum')

@section('content')

<div class="forum-admin-form-page">

    <div class="forum-admin-page-header">

        <div>
            <span class="forum-admin-eyebrow">
                FORUM / CATEGORIES / EDIT
            </span>

            <h1>Sửa danh mục</h1>

            <p>
                Cập nhật thông tin danh mục
                <strong>{{ $category->name }}</strong>.
            </p>
        </div>

    </div>


    <form
        method="POST"
        action="{{ route(
            'admin.forum.categories.update',
            $category
        ) }}"
        class="forum-category-form"
    >

        @csrf
        @method('PUT')


        <div class="forum-category-form-layout">

            {{-- Main --}}
            <div class="forum-admin-panel">

                <div class="forum-admin-panel-header">

                    <div>
                        <span class="forum-admin-panel-label">
                            INFORMATION
                        </span>

                        <h2>Thông tin danh mục</h2>
                    </div>

                </div>


                <div class="forum-category-form-body">

                    <div class="forum-form-field">

                        <label for="name">
                            Tên danh mục
                            <span>*</span>
                        </label>

                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old(
                                'name',
                                $category->name
                            ) }}"
                            required
                        >

                        @error('name')
                            <small class="forum-form-error">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>


                    <div class="forum-form-field">

                        <label for="slug">
                            Slug
                        </label>

                        <input
                            id="slug"
                            type="text"
                            name="slug"
                            value="{{ old(
                                'slug',
                                $category->slug
                            ) }}"
                        >

                        @error('slug')
                            <small class="forum-form-error">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>


                    <div class="forum-form-field">

                        <label for="description">
                            Mô tả
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="5"
                        >{{ old(
                            'description',
                            $category->description
                        ) }}</textarea>

                    </div>

                </div>

            </div>


            {{-- Settings --}}
            <div>

                <div class="forum-admin-panel">

                    <div class="forum-admin-panel-header">

                        <div>
                            <span class="forum-admin-panel-label">
                                SETTINGS
                            </span>

                            <h2>Cài đặt</h2>
                        </div>

                    </div>


                    <div class="forum-category-form-body">

                        <div class="forum-form-field">

                            <label for="icon">
                                Icon
                            </label>

                            <input
                                id="icon"
                                type="text"
                                name="icon"
                                value="{{ old(
                                    'icon',
                                    $category->icon
                                ) }}"
                                maxlength="50"
                            >

                        </div>


                        <div class="forum-form-field">

                            <label for="background_image">
                                Background image
                            </label>

                            <input
                                id="background_image"
                                type="text"
                                name="background_image"
                                value="{{ old(
                                    'background_image',
                                    $category->background_image
                                ) }}"
                            >

                        </div>


                        <div class="forum-form-field">

                            <label for="sort_order">
                                Thứ tự
                            </label>

                            <input
                                id="sort_order"
                                type="number"
                                name="sort_order"
                                value="{{ old(
                                    'sort_order',
                                    $category->sort_order
                                ) }}"
                                min="0"
                            >

                        </div>


                        <label class="forum-form-switch">

                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                @checked(
                                    old(
                                        'is_active',
                                        $category->is_active
                                    )
                                )
                            >

                            <span class="forum-form-switch-ui"></span>

                            <span>
                                <strong>Hoạt động</strong>
                                <small>
                                    Hiển thị danh mục trên Forum.
                                </small>
                            </span>

                        </label>

                    </div>

                </div>


                <div class="forum-form-actions">

                    <a
                        href="{{ route('admin.forum.categories') }}"
                        class="forum-form-cancel"
                    >
                        Hủy
                    </a>

                    <button
                        type="submit"
                        class="forum-admin-primary-btn"
                    >
                        Lưu thay đổi
                    </button>

                </div>

            </div>

        </div>

    </form>

</div>

@endsection
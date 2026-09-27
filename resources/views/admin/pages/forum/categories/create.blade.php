@extends('admin.layouts.master')

@section('title', 'Thêm danh mục Forum')

@section('content')

<div class="forum-admin-form-page">

    <div class="forum-admin-page-header">

        <div>
            <span class="forum-admin-eyebrow">
                FORUM / CATEGORIES / CREATE
            </span>

            <h1>Thêm danh mục</h1>

            <p>
                Tạo một danh mục mới cho cộng đồng Mini Cine.
            </p>
        </div>

    </div>


    <form
        method="POST"
        action="{{ route('admin.forum.categories.store') }}"
        class="forum-category-form"
    >

        @csrf

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

                    {{-- Name --}}
                    <div class="forum-form-field">

                        <label for="name">
                            Tên danh mục
                            <span>*</span>
                        </label>

                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Ví dụ: Review phim"
                            required
                        >

                        @error('name')
                            <small class="forum-form-error">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>


                    {{-- Slug --}}
                    <div class="forum-form-field">

                        <label for="slug">
                            Slug
                        </label>

                        <input
                            id="slug"
                            type="text"
                            name="slug"
                            value="{{ old('slug') }}"
                            placeholder="review-phim"
                        >

                        <small class="forum-form-help">
                            Có thể để trống để hệ thống tự tạo slug.
                        </small>

                        @error('slug')
                            <small class="forum-form-error">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>


                    {{-- Description --}}
                    <div class="forum-form-field">

                        <label for="description">
                            Mô tả
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="5"
                            placeholder="Mô tả ngắn về danh mục..."
                        >{{ old('description') }}</textarea>

                        @error('description')
                            <small class="forum-form-error">
                                {{ $message }}
                            </small>
                        @enderror

                    </div>

                </div>

            </div>


            {{-- Sidebar --}}
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

                        {{-- Icon --}}
                        <div class="forum-form-field">

                            <label for="icon">
                                Icon
                            </label>

                            <input
                                id="icon"
                                type="text"
                                name="icon"
                                value="{{ old('icon') }}"
                                placeholder="🎬"
                                maxlength="50"
                            >

                        </div>


                        {{-- Background --}}
                        <div class="forum-form-field">

                            <label for="background_image">
                                Background image
                            </label>

                            <input
                                id="background_image"
                                type="text"
                                name="background_image"
                                value="{{ old('background_image') }}"
                                placeholder="/images/forum/review.jpg"
                            >

                            <small class="forum-form-help">
                                URL ảnh nền nếu cần sử dụng ở client.
                            </small>

                        </div>


                        {{-- Sort --}}
                        <div class="forum-form-field">

                            <label for="sort_order">
                                Thứ tự
                            </label>

                            <input
                                id="sort_order"
                                type="number"
                                name="sort_order"
                                value="{{ old('sort_order', 0) }}"
                                min="0"
                            >

                        </div>


                        {{-- Active --}}
                        <label class="forum-form-switch">

                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                @checked(old('is_active', true))
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


                {{-- Actions --}}
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
                        Tạo danh mục
                    </button>

                </div>

            </div>

        </div>

    </form>

</div>

@endsection
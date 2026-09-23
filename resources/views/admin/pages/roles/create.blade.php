@extends('admin.layouts.master')

@section('content')

<section class="page">

    <div class="page-head">
        <div>
            <h3>Thêm Role</h3>
            <p>Tạo nhóm quyền mới cho quản trị viên.</p>
        </div>

        <a href="{{ route('admin.roles.index') }}" class="btn">
            ← Quay lại
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-error">
            <strong>Có lỗi xảy ra:</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf

        <div class="panel">

            <div class="panel-head">
                <h4>Thông tin Role</h4>
            </div>

            <div class="form-grid">

                <div class="field">
                    <label for="name">Tên Role</label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Ví dụ: Movie Manager"
                        required
                    >

                    @error('name')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <div class="field">
                    <label for="slug">Slug</label>

                    <input
                        type="text"
                        id="slug"
                        name="slug"
                        value="{{ old('slug') }}"
                        placeholder="Ví dụ: movie-manager"
                    >

                    <small>
                        Có thể để trống, hệ thống sẽ tự tạo từ tên Role.
                    </small>

                    @error('slug')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

                <div class="field full">
                    <label for="description">Mô tả</label>

                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        placeholder="Mô tả quyền hạn của Role..."
                    >{{ old('description') }}</textarea>

                    @error('description')
                        <small class="error-text">{{ $message }}</small>
                    @enderror
                </div>

            </div>

        </div>


        {{-- PERMISSIONS --}}
        <div class="panel">

            <div class="panel-head">
                <div>
                    <h4>Phân quyền</h4>
                    <p>Chọn những quyền mà Role này được phép sử dụng.</p>
                </div>

                <label class="select-all">
                    <input
                        type="checkbox"
                        id="checkAll"
                    >

                    <span>Chọn tất cả</span>
                </label>
            </div>


            @forelse($permissions as $module => $modulePermissions)

                <div class="permission-group">

                    <div class="permission-group-head">

                        <strong>
                            {{ ucfirst(str_replace('_', ' ', $module)) }}
                        </strong>

                        <label>
                            <input
                                type="checkbox"
                                class="module-check-all"
                                data-module="{{ $module }}"
                            >

                            Chọn module
                        </label>

                    </div>


                    <div class="permission-list">

                        @foreach($modulePermissions as $permission)

                            <label class="permission-item">

                                <input
                                    type="checkbox"
                                    name="permissions[]"
                                    value="{{ $permission->id }}"
                                    class="permission-checkbox module-{{ $module }}"
                                    data-module="{{ $module }}"
                                    {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}
                                >

                                <span>
                                    <strong>
                                        {{ $permission->name }}
                                    </strong>

                                    <small>
                                        {{ $permission->slug }}
                                    </small>

                                    @if($permission->description)
                                        <em>
                                            {{ $permission->description }}
                                        </em>
                                    @endif
                                </span>

                            </label>

                        @endforeach

                    </div>

                </div>

            @empty

                <div class="empty-state">
                    Chưa có permission nào trong hệ thống.
                </div>

            @endforelse

        </div>


        <div class="form-actions">

            <a
                href="{{ route('admin.roles.index') }}"
                class="btn"
            >
                Hủy
            </a>

            <button
                type="submit"
                class="btn btn-primary"
            >
                + Tạo Role
            </button>

        </div>

    </form>

</section>

@endsection
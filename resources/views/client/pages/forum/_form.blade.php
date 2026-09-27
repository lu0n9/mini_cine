<div class="forum-form-layout">

    {{-- MAIN FORM --}}
    <div class="forum-form-main">

        <div class="forum-form-card">

            <div class="forum-form-card-header">
                <div>
                    <span class="forum-form-eyebrow">
                        {{ isset($post) ? 'CHỈNH SỬA CHỦ ĐỀ' : 'TẠO CHỦ ĐỀ MỚI' }}
                    </span>

                    <h2>
                        {{ isset($post) ? 'Chỉnh sửa bài viết' : 'Viết bài mới' }}
                    </h2>

                    <p>
                        {{ isset($post)
                            ? 'Cập nhật nội dung và thông tin cho bài viết của bạn.'
                            : 'Chia sẻ suy nghĩ, đánh giá hoặc bắt đầu một cuộc thảo luận mới.'
                        }}
                    </p>
                </div>
            </div>

            <div class="forum-form-card-body">

                {{-- Validation errors --}}
                @if($errors->any())
                    <div class="forum-form-errors">
                        <div class="forum-form-errors-icon">!</div>

                        <div>
                            <strong>Vui lòng kiểm tra lại thông tin</strong>

                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                {{-- Category --}}
                <div class="forum-field">
                    <label for="category_id">
                        Danh mục
                        <span>*</span>
                    </label>

                    <div class="forum-select-wrap">
                        <select
                            name="category_id"
                            id="category_id"
                            class="forum-input forum-select @error('category_id') is-invalid @enderror"
                            required
                        >
                            <option value="">Chọn danh mục</option>

                            @foreach($categories as $category)
                                <option
                                    value="{{ $category->id }}"
                                    @selected(
                                        old(
                                            'category_id',
                                            isset($post) ? $post->category_id : ''
                                        ) == $category->id
                                    )
                                >
                                    {{ $category->icon ?: '💬' }}
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        <span class="forum-select-arrow">⌄</span>
                    </div>

                    @error('category_id')
                        <small class="forum-field-error">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Title --}}
                <div class="forum-field">
                    <div class="forum-label-row">
                        <label for="title">
                            Tiêu đề
                            <span>*</span>
                        </label>

                        <span class="forum-character-count">
                            <span id="titleCount">0</span>/255
                        </span>
                    </div>

                    <input
                        type="text"
                        name="title"
                        id="title"
                        class="forum-input @error('title') is-invalid @enderror"
                        value="{{ old('title', isset($post) ? $post->title : '') }}"
                        placeholder="Ví dụ: Bạn nghĩ gì về bộ phim này?"
                        maxlength="255"
                        required
                    >

                    @error('title')
                        <small class="forum-field-error">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Content --}}
                <div class="forum-field">
                    <div class="forum-label-row">
                        <label for="content">
                            Nội dung
                            <span>*</span>
                        </label>

                        <span class="forum-editor-hint">
                            Hãy chia sẻ điều bạn muốn thảo luận
                        </span>
                    </div>

                    <div class="forum-editor">
                        <div class="forum-editor-toolbar">
                            <button type="button" tabindex="-1" disabled>
                                B
                            </button>

                            <button type="button" tabindex="-1" disabled>
                                I
                            </button>

                            <span></span>

                            <button type="button" tabindex="-1" disabled>
                                • Danh sách
                            </button>

                            <button type="button" tabindex="-1" disabled>
                                🔗 Liên kết
                            </button>
                        </div>

                        <textarea
                            name="content"
                            id="content"
                            class="forum-editor-textarea @error('content') is-invalid @enderror"
                            placeholder="Viết nội dung bài viết của bạn..."
                            required
                        >{{ old('content', isset($post) ? $post->content : '') }}</textarea>

                        <div class="forum-editor-footer">
                            <span>
                                Nội dung của bạn sẽ được hiển thị công khai trên Forum.
                            </span>

                            <span>
                                <span id="contentCount">0</span> ký tự
                            </span>
                        </div>
                    </div>

                    @error('content')
                        <small class="forum-field-error">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="forum-form-actions">

                    <a
                        href="{{ isset($post)
                            ? route('forum.show', $post->slug)
                            : route('forum.index')
                        }}"
                        class="forum-secondary-btn"
                    >
                        Hủy
                    </a>

                    <button type="submit" class="forum-primary-btn forum-submit-btn">
                        <span>
                            {{ isset($post) ? '✓' : '+' }}
                        </span>

                        {{ isset($post) ? 'Lưu thay đổi' : 'Đăng bài viết' }}
                    </button>

                </div>

            </div>
        </div>

    </div>


    {{-- SIDEBAR --}}
    <aside class="forum-form-sidebar">

        {{-- Tips --}}
        <div class="forum-form-side-card">

            <div class="forum-form-side-icon">
                ✦
            </div>

            <h3>
                Gợi ý viết bài
            </h3>

            <ul>
                <li>
                    Đặt tiêu đề rõ ràng và dễ hiểu.
                </li>

                <li>
                    Chọn đúng danh mục cho chủ đề.
                </li>

                <li>
                    Chia sẻ ý kiến và trải nghiệm của bạn.
                </li>

                <li>
                    Tôn trọng những người tham gia thảo luận.
                </li>
            </ul>

        </div>


        {{-- Community --}}
        <div class="forum-form-side-card forum-form-community">

            <span class="forum-form-side-label">
                MINI CINE COMMUNITY
            </span>

            <h3>
                Cùng xây dựng cộng đồng
            </h3>

            <p>
                Mỗi bài viết là một cuộc trò chuyện mới.
                Hãy chia sẻ những điều bạn yêu thích về phim ảnh.
            </p>

        </div>


        {{-- Categories --}}
        <div class="forum-form-side-card">

            <div class="forum-form-side-title">
                <span>Danh mục</span>
                <span>{{ $categories->count() }}</span>
            </div>

            <div class="forum-form-category-list">

                @foreach($categories as $category)

                    <a
                        href="{{ route('forum.category', $category->slug) }}"
                        class="forum-form-category"
                    >
                        <span class="forum-form-category-icon">
                            {{ $category->icon ?: '💬' }}
                        </span>

                        <span>
                            {{ $category->name }}
                        </span>

                        <span class="forum-form-category-arrow">
                            ›
                        </span>
                    </a>

                @endforeach

            </div>

        </div>

    </aside>

</div>
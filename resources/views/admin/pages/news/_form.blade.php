<div class="panel page-editor-panel">
    <div class="form-group">
        <label for="title">Tiêu đề</label>
        <input id="title" name="title" value="{{ old('title', $article?->title) }}" maxlength="255" required>
        @error('title')<div class="field-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
        <label for="slug">Đường dẫn (slug)</label>
        <input id="slug" name="slug" value="{{ old('slug', $article?->slug) }}" placeholder="Tự tạo từ tiêu đề nếu để trống">
        @error('slug')<div class="field-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
        <label for="excerpt">Mô tả ngắn</label>
        <textarea id="excerpt" name="excerpt" rows="3" maxlength="500">{{ old('excerpt', $article?->excerpt) }}</textarea>
        @error('excerpt')<div class="field-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
        <label for="content">Nội dung</label>
        <textarea id="content" name="content" rows="16" required>{{ old('content', $article?->content) }}</textarea>
        @error('content')<div class="field-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
        <label for="cover_image">Ảnh bìa</label>
        <input id="cover_image" name="cover_image" type="file" accept="image/*">
        @if($article?->cover_image)<div style="margin-top:10px"><img src="{{ asset('storage/' . $article->cover_image) }}" alt="Ảnh bìa hiện tại" style="max-width:320px;max-height:180px;object-fit:cover;border-radius:8px"></div>@endif
        @error('cover_image')<div class="field-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
        <label for="status">Trạng thái</label>
        <select id="status" name="status" required>
            <option value="draft" @selected(old('status', $article?->status ?? 'draft') === 'draft')>Bản nháp</option>
            <option value="published" @selected(old('status', $article?->status) === 'published')>Xuất bản</option>
        </select>
        @error('status')<div class="field-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
        <label for="published_at">Thời gian xuất bản (để trống để dùng thời điểm hiện tại)</label>
        <input id="published_at" name="published_at" type="datetime-local" value="{{ old('published_at', $article?->published_at?->format('Y-m-d\\TH:i')) }}">
        @error('published_at')<div class="field-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-actions">
        <button type="submit" class="btn">{{ $submitLabel }}</button>
        <a href="{{ route('admin.news.index') }}" class="btn btn--ghost">Hủy</a>
    </div>
</div>

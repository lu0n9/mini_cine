@extends('admin.layouts.master')

@section('content')

<section class="page">

    <div class="page-head">

        <div>
            <h3>Thêm trang</h3>

            <p>
                Tạo một trang nội dung mới cho Mini Cine.
            </p>
        </div>

    </div>


    <form
        action="{{ route('admin.pages.store') }}"
        method="POST"
    >

        @csrf


        <div class="panel page-editor-panel">

            {{-- Tiêu đề --}}

            <div class="form-group">

                <label for="title">
                    Tiêu đề
                </label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    value="{{ old('title') }}"
                    placeholder="Ví dụ: Về chúng tôi"
                    required
                >

                @error('title')
                    <div class="field-error">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            {{-- Slug --}}

            <div class="form-group">

                <label for="slug">
                    Slug
                </label>

                <input
                    type="text"
                    id="slug"
                    name="slug"
                    value="{{ old('slug') }}"
                    placeholder="about"
                >

                @error('slug')
                    <div class="field-error">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            {{-- Mô tả ngắn --}}

            <div class="form-group">

                <label for="excerpt">
                    Mô tả ngắn
                </label>

                <textarea
                    id="excerpt"
                    name="excerpt"
                    rows="3"
                    placeholder="Mô tả ngắn về trang..."
                >{{ old('excerpt') }}</textarea>

            </div>


            {{-- Nội dung --}}

            <div class="form-group">

                <label for="content">
                    Nội dung
                </label>

                <textarea
                    id="content"
                    name="content"
                >{{ old('content') }}</textarea>

                @error('content')
                    <div class="field-error">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            {{-- Trạng thái --}}

            <div class="form-group">

                <label for="status">
                    Trạng thái
                </label>

                <select
                    id="status"
                    name="status"
                >

                    <option
                        value="draft"
                        @selected(old('status', 'draft') === 'draft')
                    >
                        Draft
                    </option>

                    <option
                        value="published"
                        @selected(old('status') === 'published')
                    >
                        Published
                    </option>

                </select>

            </div>


            {{-- Published at --}}

            <div class="form-group">

                <label for="published_at">
                    Ngày xuất bản
                </label>

                <input
                    type="datetime-local"
                    id="published_at"
                    name="published_at"
                    value="{{ old('published_at') }}"
                >

            </div>


            {{-- SEO --}}

            <hr>

            <h4>SEO</h4>


            <div class="form-group">

                <label for="seo_title">
                    SEO Title
                </label>

                <input
                    type="text"
                    id="seo_title"
                    name="seo_title"
                    value="{{ old('seo_title') }}"
                    maxlength="255"
                >

            </div>


            <div class="form-group">

                <label for="seo_description">
                    SEO Description
                </label>

                <textarea
                    id="seo_description"
                    name="seo_description"
                    rows="3"
                >{{ old('seo_description') }}</textarea>

            </div>


            <div class="form-group">

                <label for="seo_keywords">
                    SEO Keywords
                </label>

                <input
                    type="text"
                    id="seo_keywords"
                    name="seo_keywords"
                    value="{{ old('seo_keywords') }}"
                    placeholder="mini cine, xem phim, phim online"
                >

            </div>


            {{-- Actions --}}

            <div class="form-actions">

                <a
                    href="{{ route('admin.pages.index') }}"
                    class="btn-secondary"
                >
                    Hủy
                </a>

                <button
                    type="submit"
                    class="btn"
                >
                    Lưu trang
                </button>

            </div>

        </div>

    </form>

</section>


{{-- =========================================================
     TRÌNH SOẠN THẢO
     ========================================================= --}}

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        class MyUploadAdapter {
            constructor(loader) {
                this.loader = loader;
                this.xhr = null;
            }

            upload() {
                return this.loader.file.then(file => {

                    return new Promise((resolve, reject) => {

                        const data = new FormData();

                        data.append('upload', file);

                        this.xhr = new XMLHttpRequest();

                        this.xhr.open(
                            'POST',
                            '{{ route('admin.pages.upload-image') }}',
                            true
                        );

                        this.xhr.setRequestHeader(
                            'X-CSRF-TOKEN',
                            '{{ csrf_token() }}'
                        );

                        this.xhr.responseType = 'json';

                        this.xhr.addEventListener('load', () => {

                            const response = this.xhr.response;

                            if (!response || !response.url) {
                                reject(
                                    response?.message ||
                                    'Upload ảnh thất bại.'
                                );

                                return;
                            }

                            resolve({
                                default: response.url
                            });
                        });

                        this.xhr.addEventListener('error', () => {
                            reject('Không thể kết nối tới server.');
                        });

                        this.xhr.addEventListener('abort', () => {
                            reject('Upload ảnh đã bị hủy.');
                        });

                        this.xhr.send(data);
                    });
                });
            }

            abort() {
                if (this.xhr) {
                    this.xhr.abort();
                }
            }
        }

        function MyCustomUploadAdapterPlugin(editor) {
            editor.plugins.get('FileRepository').createUploadAdapter = loader => {
                return new MyUploadAdapter(loader);
            };
        }

        ClassicEditor
            .create(document.querySelector('#content'), {

                extraPlugins: [
                    MyCustomUploadAdapterPlugin
                ],

                toolbar: [
                    'heading',
                    '|',
                    'bold',
                    'italic',
                    'underline',
                    'strikethrough',
                    '|',
                    'link',
                    'uploadImage',
                    'insertTable',
                    '|',
                    'bulletedList',
                    'numberedList',
                    '|',
                    'blockQuote',
                    '|',
                    'undo',
                    'redo'
                ],

                image: {
                    toolbar: [
                        'imageTextAlternative',
                        'imageStyle:inline',
                        'imageStyle:block',
                        'imageStyle:side'
                    ]
                }

            })
            .then(editor => {

                window.pageEditor = editor;

                console.log('CKEditor đã khởi tạo.');

            })
            .catch(error => {
                console.error('CKEditor error:', error);
            });

    });
</script>

@endsection
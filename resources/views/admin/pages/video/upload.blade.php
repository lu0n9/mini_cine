@extends('admin.layouts.master')

@section('content')

<section id="video-upload" class="page">

    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <div class="page-head">

        <div>
            <h3>Upload video</h3>

            <p>
                Upload video để hệ thống tự động encode HLS
                và lưu lên Supabase.
            </p>
        </div>

    </div>


    {{-- =========================================================
         SERVER ERROR
    ========================================================== --}}

    @if ($errors->any())

        <div class="alert alert-danger">

            <strong>
                Có lỗi xảy ra:
            </strong>

            <ul style="margin:8px 0 0 18px;">

                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- =========================================================
         UPLOAD FORM
    ========================================================== --}}

    <form
        id="videoUploadForm"
        method="POST"
        action="{{ route('admin.videos.upload.store') }}"
        enctype="multipart/form-data"
    >

        @csrf


        {{-- =====================================================
             VIDEO
        ====================================================== --}}

        <div class="panel">

            <div class="panel-head">

                <h4>
                    Video
                </h4>

            </div>


            <div class="panel-body">

                <div class="form-grid">

                    {{-- MOVIE --}}

                    <div class="field">

                        <label for="movie_id">
                            Phim
                        </label>

                        <select
                            name="movie_id"
                            id="movie_id"
                            required
                        >

                            <option value="">
                                -- Chọn phim --
                            </option>

                            @foreach($movies as $movie)

                                <option
                                    value="{{ $movie->id }}"
                                    {{ old('movie_id') == $movie->id ? 'selected' : '' }}
                                >
                                    {{ $movie->title }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- EPISODE --}}

                    <div class="field">

                        <label for="episode_id">
                            Episode
                        </label>

                        <select
                            name="episode_id"
                            id="episode_id"
                            required
                        >

                            <option value="">
                                -- Chọn episode --
                            </option>

                            @foreach($episodes as $episode)

                                <option
                                    value="{{ $episode->id }}"
                                    data-movie="{{ $episode->movie_id }}"
                                    {{ old('episode_id') == $episode->id ? 'selected' : '' }}
                                >

                                    {{ $episode->movie?->title }}

                                    —

                                    S{{ str_pad($episode->season?->season_number ?? 1, 2, '0', STR_PAD_LEFT) }}

                                    E{{ str_pad($episode->episode_number, 2, '0', STR_PAD_LEFT) }}

                                    @if($episode->name)
                                        — {{ $episode->name }}
                                    @endif

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- VIDEO FILE --}}

                    <div class="field full">

                        <label for="video">
                            Video
                        </label>

                        <input
                            type="file"
                            name="video"
                            id="video"
                            accept="video/mp4,video/quicktime,video/webm,video/x-matroska"
                            required
                        >

                        <small>
                            Hỗ trợ MP4, MOV, WebM, MKV.
                            Tối đa 5GB.
                        </small>

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================================================
             SUBTITLE
        ========================================================== --}}

        <div class="panel">

            <div class="panel-head">

                <h4>
                    Phụ đề
                </h4>

            </div>


            <div class="panel-body">

                <div class="form-grid">

                    {{-- MULTIPLE SUBTITLE --}}

                    <div class="field full">

                        <label for="subtitles">
                            Phụ đề
                        </label>

                        <input
                            type="file"
                            id="subtitles"
                            name="subtitles[]"
                            accept=".vtt,.srt,text/vtt,application/x-subrip"
                            multiple
                        >

                        <small>
                            Có thể chọn nhiều file VTT/SRT cùng lúc.
                            Tối đa 10 file.
                        </small>

                        <small>
                            Đặt tên file theo ngôn ngữ:
                            <strong>vi.vtt</strong>,
                            <strong>en.vtt</strong>,
                            <strong>zh-cn.vtt</strong>...
                        </small>

                    </div>


                    {{-- SELECTED SUBTITLE LIST --}}

                    <div
                        class="field full"
                        id="subtitleListBox"
                        style="display:none;"
                    >

                        <label>
                            Subtitle đã chọn
                        </label>

                        <div
                            id="subtitleList"
                            style="
                                display:flex;
                                flex-direction:column;
                                gap:8px;
                            "
                        ></div>

                    </div>


                    {{-- DEFAULT LANGUAGE --}}

                    <div
                        class="field full"
                        id="subtitleDefaultBox"
                        style="display:none;"
                    >

                        <label for="subtitle_default_language">
                            Phụ đề mặc định
                        </label>

                        <select
                            id="subtitle_default_language"
                            name="subtitle_default_language"
                        >

                            <option value="">
                                Không chọn
                            </option>

                        </select>

                        <small>
                            Nếu chọn, subtitle này sẽ được đánh dấu
                            <code>is_default = 1</code>.
                        </small>

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================================================
             PROGRESS
        ========================================================== --}}

        <div
            id="progressBox"
            class="panel"
            style="display:none;"
        >

            <div class="panel-head">

                <h4>
                    Tiến trình
                </h4>

            </div>


            <div class="panel-body">

                <div
                    style="
                        width:100%;
                        height:10px;
                        background:#e5e7eb;
                        border-radius:999px;
                        overflow:hidden;
                    "
                >

                    <div
                        id="progressBar"
                        style="
                            width:0%;
                            height:100%;
                            transition:width .25s ease;
                        "
                    ></div>

                </div>


                <div
                    style="
                        display:flex;
                        justify-content:space-between;
                        align-items:center;
                        margin-top:10px;
                    "
                >

                    <strong id="progressText">
                        0%
                    </strong>

                    <span id="statusText">
                        Chưa bắt đầu
                    </span>

                </div>

            </div>

        </div>


        {{-- =========================================================
             RESULT
        ========================================================== --}}

        <div
            id="resultBox"
            class="alert"
            style="display:none;"
        >

            <div id="resultMessage"></div>

        </div>


        {{-- =========================================================
             BUTTON
        ========================================================== --}}

        <div
            style="
                display:flex;
                justify-content:flex-end;
                margin-top:18px;
            "
        >

            <button
                type="submit"
                class="btn"
                id="uploadButton"
            >
                Upload & xử lý HLS
            </button>

        </div>

    </form>

</section>


{{-- =============================================================
     JAVASCRIPT
============================================================== --}}

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        /*
         * =========================================================
         * ELEMENTS
         * =========================================================
         */

        const movieSelect =
            document.getElementById(
                'movie_id'
            );

        const episodeSelect =
            document.getElementById(
                'episode_id'
            );

        const form =
            document.getElementById(
                'videoUploadForm'
            );

        const button =
            document.getElementById(
                'uploadButton'
            );

        const progressBox =
            document.getElementById(
                'progressBox'
            );

        const progressBar =
            document.getElementById(
                'progressBar'
            );

        const progressText =
            document.getElementById(
                'progressText'
            );

        const statusText =
            document.getElementById(
                'statusText'
            );

        const resultBox =
            document.getElementById(
                'resultBox'
            );

        const resultMessage =
            document.getElementById(
                'resultMessage'
            );

        const videoInput =
            document.getElementById(
                'video'
            );

        const subtitleInput =
            document.getElementById(
                'subtitles'
            );

        const subtitleListBox =
            document.getElementById(
                'subtitleListBox'
            );

        const subtitleList =
            document.getElementById(
                'subtitleList'
            );

        const subtitleDefaultBox =
            document.getElementById(
                'subtitleDefaultBox'
            );

        const subtitleDefaultLanguage =
            document.getElementById(
                'subtitle_default_language'
            );


        /*
         * =========================================================
         * CHECK
         * =========================================================
         */

        if (!form) {

            console.error(
                'Không tìm thấy #videoUploadForm'
            );

            return;
        }

        if (!videoInput) {

            console.error(
                'Không tìm thấy #video'
            );

            return;
        }


        /*
         * =========================================================
         * MOVIE → EPISODE
         * =========================================================
         */

        function filterEpisodes() {

            if (
                !movieSelect ||
                !episodeSelect
            ) {
                return;
            }

            const movieId =
                movieSelect.value;


            Array.from(
                episodeSelect.options
            ).forEach(
                function (option) {

                    if (!option.value) {

                        option.hidden =
                            false;

                        return;
                    }


                    option.hidden =
                        option.dataset.movie !==
                        movieId;
                }
            );


            /*
             * Nếu episode hiện tại
             * không thuộc movie:
             * reset.
             */

            const selected =
                episodeSelect.options[
                    episodeSelect.selectedIndex
                ];

            if (
                selected &&
                selected.dataset.movie !==
                movieId
            ) {

                episodeSelect.value =
                    '';
            }
        }


        if (movieSelect) {

            movieSelect.addEventListener(
                'change',
                filterEpisodes
            );

            filterEpisodes();
        }


        /*
         * =========================================================
         * LANGUAGE
         * =========================================================
         */

        const languageLabels = {

            vi: 'Tiếng Việt',

            en: 'English',

            zh: '中文',

            'zh-cn': '简体中文',

            'zh-tw': '繁體中文',

            ko: '한국어',

            ja: '日本語',

            th: 'ไทย',

            id: 'Bahasa Indonesia',

            fr: 'Français',

            de: 'Deutsch',

            es: 'Español',

            pt: 'Português',

            ru: 'Русский'
        };


        function detectSubtitleLanguage(
            filename
        ) {

            const name =
                filename
                    .toLowerCase()
                    .replace(
                        /\.(vtt|srt)$/i,
                        ''
                    );


            const parts =
                name.split('.');


            return (
                parts[
                    parts.length - 1
                ] || ''
            ).trim();
        }


        function getSubtitleLabel(
            language
        ) {

            return (
                languageLabels[
                    language
                ] ||
                language.toUpperCase()
            );
        }


        function formatFileSize(
            bytes
        ) {

            if (
                !bytes
            ) {
                return '0 B';
            }


            const units = [
                'B',
                'KB',
                'MB',
                'GB'
            ];


            const index =
                Math.min(
                    Math.floor(
                        Math.log(bytes) /
                        Math.log(1024)
                    ),
                    units.length - 1
                );


            return (
                bytes /
                Math.pow(
                    1024,
                    index
                )
            ).toFixed(2)
            + ' '
            + units[index];
        }


        /*
         * =========================================================
         * RENDER SUBTITLE
         * =========================================================
         */

        function renderSubtitleList() {

            if (
                !subtitleInput ||
                !subtitleList
            ) {
                return;
            }


            subtitleList.innerHTML =
                '';


            const files =
                Array.from(
                    subtitleInput.files || []
                );


            if (!files.length) {

                if (subtitleListBox) {

                    subtitleListBox.style.display =
                        'none';
                }

                if (subtitleDefaultBox) {

                    subtitleDefaultBox.style.display =
                        'none';
                }

                if (
                    subtitleDefaultLanguage
                ) {

                    subtitleDefaultLanguage.innerHTML =
                        '<option value="">Không chọn</option>';
                }

                return;
            }


            if (subtitleListBox) {

                subtitleListBox.style.display =
                    'block';
            }

            if (subtitleDefaultBox) {

                subtitleDefaultBox.style.display =
                    'block';
            }


            const languages = [];


            files.forEach(
                function (file) {

                    const language =
                        detectSubtitleLanguage(
                            file.name
                        );


                    if (
                        !languages.includes(
                            language
                        )
                    ) {

                        languages.push(
                            language
                        );
                    }


                    const item =
                        document.createElement(
                            'div'
                        );


                    item.style.cssText = `
                        display:flex;
                        align-items:center;
                        justify-content:space-between;
                        gap:12px;
                        padding:10px 12px;
                        border:1px solid #ddd;
                        border-radius:8px;
                    `;


                    const info =
                        document.createElement(
                            'div'
                        );


                    const name =
                        document.createElement(
                            'strong'
                        );

                    name.textContent =
                        file.name;


                    const detail =
                        document.createElement(
                            'small'
                        );

                    detail.style.cssText =
                        'display:block;margin-top:3px;opacity:.7;';


                    detail.textContent =
                        language +
                        ' — ' +
                        getSubtitleLabel(
                            language
                        );


                    info.appendChild(
                        name
                    );

                    info.appendChild(
                        detail
                    );


                    const size =
                        document.createElement(
                            'small'
                        );

                    size.textContent =
                        formatFileSize(
                            file.size
                        );

                    size.style.opacity =
                        '.7';


                    item.appendChild(
                        info
                    );

                    item.appendChild(
                        size
                    );


                    subtitleList.appendChild(
                        item
                    );
                }
            );


            /*
             * Default language.
             */

            if (
                subtitleDefaultLanguage
            ) {

                subtitleDefaultLanguage.innerHTML =
                    '';


                const empty =
                    document.createElement(
                        'option'
                    );

                empty.value =
                    '';

                empty.textContent =
                    'Không chọn';


                subtitleDefaultLanguage.appendChild(
                    empty
                );


                languages.forEach(
                    function (language) {

                        const option =
                            document.createElement(
                                'option'
                            );

                        option.value =
                            language;

                        option.textContent =
                            getSubtitleLabel(
                                language
                            ) +
                            ' (' +
                            language +
                            ')';


                        subtitleDefaultLanguage.appendChild(
                            option
                        );
                    }
                );


                /*
                 * Ưu tiên tiếng Việt.
                 */

                if (
                    languages.includes(
                        'vi'
                    )
                ) {

                    subtitleDefaultLanguage.value =
                        'vi';
                }
            }
        }


        /*
         * =========================================================
         * SUBTITLE INPUT
         * =========================================================
         */

        if (subtitleInput) {

            subtitleInput.addEventListener(
                'change',
                function () {

                    const files =
                        Array.from(
                            this.files || []
                        );


                    /*
                     * Max 10.
                     */

                    if (
                        files.length > 10
                    ) {

                        alert(
                            'Bạn chỉ được chọn tối đa 10 file phụ đề.'
                        );

                        this.value =
                            '';

                        renderSubtitleList();

                        return;
                    }


                    const languages = [];


                    for (
                        const file of files
                    ) {

                        const filename =
                            file.name.toLowerCase();


                        /*
                         * VTT/SRT.
                         */

                        if (
                            !filename.endsWith(
                                '.vtt'
                            ) &&
                            !filename.endsWith(
                                '.srt'
                            )
                        ) {

                            alert(
                                'File "' +
                                file.name +
                                '" không phải VTT hoặc SRT.'
                            );

                            this.value =
                                '';

                            renderSubtitleList();

                            return;
                        }


                        const language =
                            detectSubtitleLanguage(
                                file.name
                            );


                        if (
                            !language ||
                            !/^[a-z]{2,3}(?:-[a-z]{2,4})?$/.test(
                                language
                            )
                        ) {

                            alert(
                                'Không xác định được ngôn ngữ từ file "' +
                                file.name +
                                '". Hãy đặt tên dạng vi.vtt hoặc en.srt.'
                            );

                            this.value =
                                '';

                            renderSubtitleList();

                            return;
                        }


                        /*
                         * Không trùng language.
                         */

                        if (
                            languages.includes(
                                language
                            )
                        ) {

                            alert(
                                'Bạn đang chọn nhiều subtitle cùng ngôn ngữ: ' +
                                language
                            );

                            this.value =
                                '';

                            renderSubtitleList();

                            return;
                        }


                        languages.push(
                            language
                        );
                    }


                    renderSubtitleList();
                }
            );
        }


        /*
         * =========================================================
         * SUBMIT
         * =========================================================
         */

        form.addEventListener(
            'submit',
            function (event) {

                event.preventDefault();


                /*
                 * -------------------------------------------------
                 * VIDEO
                 * -------------------------------------------------
                 */

                if (
                    !videoInput.files ||
                    !videoInput.files.length
                ) {

                    alert(
                        'Vui lòng chọn file video.'
                    );

                    return;
                }


                /*
                 * -------------------------------------------------
                 * EPISODE
                 * -------------------------------------------------
                 */

                if (
                    !episodeSelect ||
                    !episodeSelect.value
                ) {

                    alert(
                        'Vui lòng chọn episode.'
                    );

                    return;
                }


                /*
                 * -------------------------------------------------
                 * SUBTITLE
                 * -------------------------------------------------
                 */

                const subtitleFiles =
                    subtitleInput
                        ? Array.from(
                            subtitleInput.files || []
                        )
                        : [];


                const languages = [];


                for (
                    const file of subtitleFiles
                ) {

                    const language =
                        detectSubtitleLanguage(
                            file.name
                        );


                    if (
                        languages.includes(
                            language
                        )
                    ) {

                        alert(
                            'Có nhiều subtitle cùng ngôn ngữ: ' +
                            language
                        );

                        return;
                    }


                    languages.push(
                        language
                    );
                }


                if (
                    subtitleDefaultLanguage &&
                    subtitleDefaultLanguage.value
                ) {

                    if (
                        !languages.includes(
                            subtitleDefaultLanguage.value
                        )
                    ) {

                        alert(
                            'Subtitle mặc định không nằm trong danh sách subtitle đã chọn.'
                        );

                        return;
                    }
                }


                /*
                 * =================================================
                 * FORMDATA
                 * =================================================
                 */

                const formData =
                    new FormData(
                        form
                    );


                /*
                 * Debug.
                 */

                console.log(
                    '========== MINI CINE UPLOAD =========='
                );

                console.log(
                    'movie_id:',
                    formData.get(
                        'movie_id'
                    )
                );

                console.log(
                    'episode_id:',
                    formData.get(
                        'episode_id'
                    )
                );

                console.log(
                    'video:',
                    formData.get(
                        'video'
                    )
                );

                console.log(
                    'subtitle count:',
                    subtitleFiles.length
                );


                subtitleFiles.forEach(
                    function (file) {

                        console.log(
                            'subtitle:',
                            file.name
                        );
                    }
                );


                console.log(
                    'default:',
                    formData.get(
                        'subtitle_default_language'
                    )
                );


                /*
                 * =================================================
                 * UI
                 * =================================================
                 */

                if (progressBox) {

                    progressBox.style.display =
                        'block';
                }

                if (resultBox) {

                    resultBox.style.display =
                        'none';
                }

                if (progressBar) {

                    progressBar.style.width =
                        '0%';
                }

                if (progressText) {

                    progressText.textContent =
                        '0%';
                }

                if (statusText) {

                    statusText.textContent =
                        'Đang upload...';
                }


                button.disabled =
                    true;

                button.textContent =
                    'Đang upload...';


                /*
                 * =================================================
                 * XHR
                 * =================================================
                 */

                const xhr =
                    new XMLHttpRequest();


                xhr.open(
                    'POST',
                    '{{ route('admin.videos.upload.store') }}',
                    true
                );


                /*
                 * CSRF
                 */

                const csrfInput =
                    form.querySelector(
                        'input[name="_token"]'
                    );


                if (csrfInput) {

                    xhr.setRequestHeader(
                        'X-CSRF-TOKEN',
                        csrfInput.value
                    );
                }


                xhr.setRequestHeader(
                    'Accept',
                    'application/json'
                );


                /*
                 * =================================================
                 * UPLOAD PROGRESS
                 * =================================================
                 */

                xhr.upload.addEventListener(
                    'progress',
                    function (event) {

                        if (
                            !event.lengthComputable
                        ) {
                            return;
                        }


                        const percent =
                            Math.round(
                                (
                                    event.loaded /
                                    event.total
                                ) * 100
                            );


                        if (progressBar) {

                            progressBar.style.width =
                                percent + '%';
                        }


                        if (progressText) {

                            progressText.textContent =
                                percent + '%';
                        }


                        if (statusText) {

                            statusText.textContent =
                                subtitleFiles.length
                                    ? 'Đang upload video + phụ đề...'
                                    : 'Đang upload video...';
                        }
                    }
                );


                /*
                 * =================================================
                 * LOAD
                 * =================================================
                 */

                xhr.addEventListener(
                    'load',
                    function () {

                        console.log(
                            'HTTP STATUS:',
                            xhr.status
                        );

                        console.log(
                            'CONTENT TYPE:',
                            xhr.getResponseHeader(
                                'Content-Type'
                            )
                        );

                        console.log(
                            'RAW RESPONSE:',
                            xhr.responseText
                        );


                        /*
                         * -------------------------------------------------
                         * HTTP ERROR
                         * -------------------------------------------------
                         */

                        if (
                            xhr.status < 200 ||
                            xhr.status >= 300
                        ) {

                            button.disabled =
                                false;

                            button.textContent =
                                'Upload & xử lý HLS';


                            if (statusText) {

                                statusText.textContent =
                                    'Upload thất bại';
                            }


                            if (resultBox) {

                                resultBox.style.display =
                                    'block';
                            }


                            let message =
                                'Upload thất bại.';


                            try {

                                const data =
                                    JSON.parse(
                                        xhr.responseText
                                    );


                                if (
                                    data.errors
                                ) {

                                    const errors =
                                        [];


                                    Object.values(
                                        data.errors
                                    ).forEach(
                                        function (
                                            messages
                                        ) {

                                            messages.forEach(
                                                function (
                                                    msg
                                                ) {

                                                    errors.push(
                                                        msg
                                                    );
                                                }
                                            );
                                        }
                                    );


                                    if (
                                        errors.length
                                    ) {

                                        message =
                                            errors.join(
                                                '<br>'
                                            );
                                    }

                                } else if (
                                    data.message
                                ) {

                                    message =
                                        data.message;
                                }

                            } catch (error) {

                                /*
                                 * Server không trả JSON.
                                 *
                                 * Hiển thị response thật
                                 * để debug.
                                 */

                                console.error(
                                    'RAW SERVER ERROR:',
                                    xhr.responseText
                                );


                                message =
                                    'Server HTTP ' +
                                    xhr.status +
                                    '<br><pre style="white-space:pre-wrap;">' +
                                    escapeHtml(
                                        xhr.responseText
                                    ) +
                                    '</pre>';
                            }


                            if (resultMessage) {

                                resultMessage.innerHTML =
                                    message;
                            }


                            return;
                        }


                        /*
                         * -------------------------------------------------
                         * JSON RESPONSE
                         * -------------------------------------------------
                         */

                        let response;


                        try {

                            response =
                                JSON.parse(
                                    xhr.responseText
                                );

                        } catch (error) {

                            console.error(
                                'JSON parse error:',
                                error
                            );

                            console.error(
                                'RAW RESPONSE:',
                                xhr.responseText
                            );


                            button.disabled =
                                false;

                            button.textContent =
                                'Upload & xử lý HLS';


                            if (statusText) {

                                statusText.textContent =
                                    'Server trả về dữ liệu không hợp lệ.';
                            }


                            if (resultBox) {

                                resultBox.style.display =
                                    'block';
                            }


                            if (resultMessage) {

                                resultMessage.innerHTML =
                                    '<strong>Server không trả JSON.</strong>' +
                                    '<pre style="white-space:pre-wrap;margin-top:10px;">' +
                                    escapeHtml(
                                        xhr.responseText
                                    ) +
                                    '</pre>';
                            }


                            return;
                        }


                        /*
                         * -------------------------------------------------
                         * SERVER REPORTED ERROR
                         * -------------------------------------------------
                         */

                        if (
                            response.success ===
                            false
                        ) {

                            button.disabled =
                                false;

                            button.textContent =
                                'Upload & xử lý HLS';


                            if (statusText) {

                                statusText.textContent =
                                    'Upload thất bại';
                            }


                            if (resultBox) {

                                resultBox.style.display =
                                    'block';
                            }


                            if (resultMessage) {

                                resultMessage.textContent =
                                    response.message ||
                                    'Upload thất bại.';
                            }


                            return;
                        }


                        /*
                         * -------------------------------------------------
                         * QUEUED
                         * -------------------------------------------------
                         */

                        if (
                            response.job_id
                        ) {

                            if (progressBar) {

                                progressBar.style.width =
                                    '100%';
                            }

                            if (progressText) {

                                progressText.textContent =
                                    '100%';
                            }

                            if (statusText) {

                                statusText.textContent =
                                    'Đã upload. Đang xử lý HLS...';
                            }


                            pollProcessing(
                                response.job_id
                            );

                        } else {

                            button.disabled =
                                false;

                            button.textContent =
                                'Upload & xử lý HLS';


                            if (statusText) {

                                statusText.textContent =
                                    'Không nhận được Job ID.';
                            }
                        }
                    }
                );


                /*
                 * =================================================
                 * NETWORK ERROR
                 * =================================================
                 */

                xhr.addEventListener(
                    'error',
                    function () {

                        button.disabled =
                            false;

                        button.textContent =
                            'Upload & xử lý HLS';


                        if (statusText) {

                            statusText.textContent =
                                'Lỗi kết nối.';
                        }


                        if (resultBox) {

                            resultBox.style.display =
                                'block';
                        }


                        if (resultMessage) {

                            resultMessage.innerHTML =
                                '<strong>Không thể kết nối đến server.</strong>';
                        }
                    }
                );


                /*
                 * =================================================
                 * ABORT
                 * =================================================
                 */

                xhr.addEventListener(
                    'abort',
                    function () {

                        button.disabled =
                            false;

                        button.textContent =
                            'Upload & xử lý HLS';


                        if (statusText) {

                            statusText.textContent =
                                'Upload đã bị hủy.';
                        }
                    }
                );


                /*
                 * =================================================
                 * SEND
                 * =================================================
                 */

                xhr.send(
                    formData
                );
            }
        );


        /*
         * =========================================================
         * POLLING
         * =========================================================
         */

        function pollProcessing(
            jobId
        ) {

            const url =
                '{{ url('/admin/videos/processing') }}/' +
                jobId +
                '/status';


            const timer =
                setInterval(
                    async function () {

                        try {

                            const response =
                                await fetch(
                                    url,
                                    {
                                        method:
                                            'GET',

                                        headers: {
                                            'Accept':
                                                'application/json',

                                            'X-Requested-With':
                                                'XMLHttpRequest'
                                        }
                                    }
                                );


                            const text =
                                await response.text();


                            console.log(
                                'POLL RESPONSE:',
                                text
                            );


                            let data;


                            try {

                                data =
                                    JSON.parse(
                                        text
                                    );

                            } catch (
                                error
                            ) {

                                throw new Error(
                                    'Polling không trả JSON: ' +
                                    text
                                );
                            }


                            if (
                                !response.ok
                            ) {

                                throw new Error(
                                    data.error ||
                                    data.message ||
                                    'HTTP ' +
                                    response.status
                                );
                            }


                            const progress =
                                Number(
                                    data.progress ||
                                    0
                                );


                            if (progressBar) {

                                progressBar.style.width =
                                    progress + '%';
                            }


                            if (progressText) {

                                progressText.textContent =
                                    progress + '%';
                            }


                            /*
                             * PROCESSING
                             */

                            if (
                                data.status ===
                                'processing'
                            ) {

                                if (statusText) {

                                    statusText.textContent =
                                        'FFmpeg đang encode HLS...';
                                }
                            }


                            /*
                             * UPLOADING
                             */

                            if (
                                data.status ===
                                'uploading'
                            ) {

                                if (statusText) {

                                    statusText.textContent =
                                        'Đang upload HLS + subtitle lên Supabase...';
                                }
                            }


                            /*
                             * PENDING
                             */

                            if (
                                data.status ===
                                'pending'
                            ) {

                                if (statusText) {

                                    statusText.textContent =
                                        'Đang chờ Queue Worker...';
                                }
                            }


                            /*
                             * COMPLETED
                             */

                            if (
                                data.status ===
                                'completed'
                            ) {

                                clearInterval(
                                    timer
                                );


                                if (progressBar) {

                                    progressBar.style.width =
                                        '100%';
                                }

                                if (progressText) {

                                    progressText.textContent =
                                        '100%';
                                }

                                if (statusText) {

                                    statusText.textContent =
                                        'Hoàn thành';
                                }


                                button.disabled =
                                    false;

                                button.textContent =
                                    'Upload & xử lý HLS';


                                if (resultBox) {

                                    resultBox.style.display =
                                        'block';
                                }


                                if (resultMessage) {

                                    resultMessage.innerHTML =
                                        '<strong>✓ Video + HLS đã được xử lý thành công.</strong>';
                                }


                                return;
                            }


                            /*
                             * FAILED
                             */

                            if (
                                data.status ===
                                'failed'
                            ) {

                                clearInterval(
                                    timer
                                );


                                button.disabled =
                                    false;

                                button.textContent =
                                    'Upload & xử lý HLS';


                                if (statusText) {

                                    statusText.textContent =
                                        'Xử lý thất bại';
                                }


                                if (resultBox) {

                                    resultBox.style.display =
                                        'block';
                                }


                                if (resultMessage) {

                                    resultMessage.innerHTML =
                                        '<strong>✕ Xử lý thất bại:</strong><br>' +
                                        escapeHtml(
                                            data.error ||
                                            'Không xác định.'
                                        );
                                }
                            }

                        } catch (error) {

                            console.error(
                                'Polling error:',
                                error
                            );

                        }

                    },
                    3000
                );
        }


        /*
         * =========================================================
         * ESCAPE HTML
         * =========================================================
         */

        function escapeHtml(
            value
        ) {

            const div =
                document.createElement(
                    'div'
                );

            div.textContent =
                value == null
                    ? ''
                    : String(value);

            return div.innerHTML;
        }

    }
);

</script>

@endsection
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
                và lưu lên server cloud.
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
                                    data-season="{{ $episode->season_id }}"
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

                        <button type="button" class="btn ghost" style="margin-top:10px" onclick="openQuickStructureModal()">
                            + Thêm Season / Episode cho Movie
                        </button>

                    </div>


                    {{-- SERVER SELECTOR --}}

                    <div class="field full">

                        <label for="server_id">
                            Server lưu trữ
                        </label>

                        <select
                            name="server_id"
                            id="server_id"
                        >

                            <option value="" {{ old('server_id') ? '' : 'selected' }}>
                                -- Dùng cấu hình mặc định --
                            </option>

                            @if(isset($storageServers))
                                @foreach($storageServers as $server)

                                    <option value="{{ $server->id }}" {{ old('server_id') == $server->id ? 'selected' : '' }}>
                                        {{ $server->name }}
                                        @if($server->endpoint)
                                            — {{ $server->endpoint }}
                                        @endif
                                    </option>

                                @endforeach
                            @endif

                        </select>

                        <small>
                            Chọn một API Video Storage &amp; CDN đang hoạt động. Nếu để mặc định, hệ thống dùng cấu hình lưu trữ mặc định.
                            Các server được quản lý tại
                            <a href="{{ route('admin.system.api') }}" style="color: #3b82f6;">
                                Quản lý API
                            </a>.
                        </small>
                        @if($storageServers->isEmpty())
                            <small style="display:block; margin-top:6px; color:#f59e0b;">
                                Chưa có API Video Storage &amp; CDN đang hoạt động. Hãy thêm hoặc bật API trong Quản lý API.
                            </small>
                        @endif

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

    <div id="quickStructureModal" role="dialog" aria-modal="true" aria-labelledby="quickStructureTitle" style="display:none;position:fixed;inset:0;z-index:10000;background:rgba(0,0,0,.72);padding:20px;align-items:center;justify-content:center">
        <div class="panel" style="width:100%;max-width:620px;max-height:90vh;overflow:auto">
            <div class="panel-head" style="display:flex;align-items:center;justify-content:space-between">
                <h4 id="quickStructureTitle">Thêm Season / Episode nhanh</h4>
                <button type="button" class="btn ghost" onclick="closeQuickStructureModal()">Đóng</button>
            </div>
            <div class="panel-body">
                <p style="color:var(--muted);margin-top:0">Movie đang chọn: <strong id="quickMovieName">Chưa chọn</strong></p>
                <div class="form-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    <div class="field"><label for="quickSeasonNumber">Số Season</label><input id="quickSeasonNumber" type="number" min="1" value="1"></div>
                    <div class="field"><label for="quickSeasonName">Tên Season (không bắt buộc)</label><input id="quickSeasonName" type="text" maxlength="255" placeholder="Ví dụ: Phần 1"></div>
                </div>
                <button id="quickCreateSeason" type="button" class="btn" style="margin-top:12px">Tạo Season</button>

                <hr style="border-color:var(--line);margin:20px 0">
                <div class="field"><label for="quickEpisodeSeason">Season của Episode</label>
                    <select id="quickEpisodeSeason"><option value="">-- Chọn hoặc tạo Season --</option>
                        @foreach($seasons as $season)
                            <option value="{{ $season->id }}" data-movie="{{ $season->movie_id }}" data-season-number="{{ $season->season_number }}">{{ $season->movie?->title }} — {{ $season->name ?: 'Season ' . $season->season_number }} (S{{ str_pad($season->season_number, 2, '0', STR_PAD_LEFT) }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:12px">
                    <div class="field"><label for="quickEpisodeNumber">Số Episode</label><input id="quickEpisodeNumber" type="number" min="1" value="1"></div>
                    <div class="field"><label for="quickEpisodeName">Tên Episode (không bắt buộc)</label><input id="quickEpisodeName" type="text" maxlength="255" placeholder="Mặc định: Tập N"></div>
                    <div class="field full"><label for="quickEpisodeDuration">Thời lượng (giây, không bắt buộc)</label><input id="quickEpisodeDuration" type="number" min="1" placeholder="Ví dụ: 2400"></div>
                </div>
                <button id="quickCreateEpisode" type="button" class="btn" style="margin-top:12px">Tạo Episode và chọn để upload</button>
                <p id="quickStructureMessage" role="status" style="margin:12px 0 0"></p>
            </div>
        </div>
    </div>

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

        const quickStructureModal = document.getElementById('quickStructureModal');
        const quickEpisodeSeason = document.getElementById('quickEpisodeSeason');
        const quickStructureMessage = document.getElementById('quickStructureMessage');

        function showQuickMessage(message, isError = false) {
            if (!quickStructureMessage) return;
            quickStructureMessage.textContent = message;
            quickStructureMessage.style.color = isError ? '#ef4444' : '#10b981';
        }

        function filterQuickSeasons() {
            if (!movieSelect || !quickEpisodeSeason) return;
            const movieId = movieSelect.value;
            Array.from(quickEpisodeSeason.options).forEach(option => {
                option.hidden = option.value !== '' && option.dataset.movie !== movieId;
            });
            const selected = quickEpisodeSeason.options[quickEpisodeSeason.selectedIndex];
            if (selected && selected.dataset.movie !== movieId) quickEpisodeSeason.value = '';
        }

        window.openQuickStructureModal = function () {
            if (!movieSelect || !movieSelect.value) {
                alert('Hãy chọn Movie trước khi thêm Season hoặc Episode.');
                return;
            }
            const movieOption = movieSelect.options[movieSelect.selectedIndex];
            document.getElementById('quickMovieName').textContent = movieOption.textContent.trim();
            quickStructureModal.style.display = 'flex';
            showQuickMessage('');
            filterQuickSeasons();

            const currentMovieSeasons = Array.from(quickEpisodeSeason.options)
                .filter(option => option.value && option.dataset.movie === movieSelect.value);
            const nextSeason = currentMovieSeasons.reduce((maximum, option) => Math.max(maximum, Number(option.dataset.seasonNumber || 0)), 0) + 1;
            document.getElementById('quickSeasonNumber').value = nextSeason;
            if (currentMovieSeasons.length && !quickEpisodeSeason.value) {
                quickEpisodeSeason.value = currentMovieSeasons[currentMovieSeasons.length - 1].value;
            }
        };

        window.closeQuickStructureModal = function () {
            quickStructureModal.style.display = 'none';
        };

        quickStructureModal?.addEventListener('click', event => {
            if (event.target === quickStructureModal) window.closeQuickStructureModal();
        });

        async function sendQuickCreate(url, payload, button) {
            button.disabled = true;
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(payload)
                });
                const data = await response.json();
                if (!response.ok) {
                    const firstError = data.errors ? Object.values(data.errors).flat()[0] : null;
                    throw new Error(firstError || data.message || 'Không thể tạo dữ liệu.');
                }
                return data;
            } finally {
                button.disabled = false;
            }
        }

        document.getElementById('quickCreateSeason')?.addEventListener('click', async function () {
            if (!movieSelect?.value) return showQuickMessage('Hãy chọn Movie trước.', true);
            try {
                const data = await sendQuickCreate('{{ route('admin.videos.upload.seasons.store') }}', {
                    movie_id: movieSelect.value,
                    season_number: document.getElementById('quickSeasonNumber').value,
                    name: document.getElementById('quickSeasonName').value || null
                }, this);
                const option = new Option(
                    movieSelect.options[movieSelect.selectedIndex].textContent.trim() + ' — ' + data.season.label,
                    data.season.id
                );
                option.dataset.movie = data.season.movie_id;
                option.dataset.seasonNumber = data.season.season_number;
                quickEpisodeSeason.add(option);
                filterQuickSeasons();
                quickEpisodeSeason.value = String(data.season.id);
                document.getElementById('quickSeasonName').value = '';
                showQuickMessage('Đã tạo Season. Bạn có thể tạo Episode bên dưới.');
            } catch (error) {
                showQuickMessage(error.message, true);
            }
        });

        document.getElementById('quickCreateEpisode')?.addEventListener('click', async function () {
            if (!movieSelect?.value) return showQuickMessage('Hãy chọn Movie trước.', true);
            if (!quickEpisodeSeason?.value) return showQuickMessage('Hãy chọn hoặc tạo Season trước.', true);
            try {
                const episodeNumber = document.getElementById('quickEpisodeNumber').value;
                const data = await sendQuickCreate('{{ route('admin.videos.upload.episodes.store') }}', {
                    movie_id: movieSelect.value,
                    season_id: quickEpisodeSeason.value,
                    episode_number: episodeNumber,
                    name: document.getElementById('quickEpisodeName').value || null,
                    duration: document.getElementById('quickEpisodeDuration').value || null
                }, this);
                const option = new Option(data.episode.label, data.episode.id, true, true);
                option.dataset.movie = data.episode.movie_id;
                option.dataset.season = data.episode.season_id;
                episodeSelect.add(option);
                episodeSelect.value = String(data.episode.id);
                document.getElementById('quickEpisodeNumber').value = Number(episodeNumber) + 1;
                document.getElementById('quickEpisodeName').value = '';
                document.getElementById('quickEpisodeDuration').value = '';
                showQuickMessage('Đã tạo và chọn Episode. Đóng cửa sổ để tiếp tục upload video.');
            } catch (error) {
                showQuickMessage(error.message, true);
            }
        });

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
            filterQuickSeasons();
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
                                    resultMessage.innerHTML = data.local_source_removed === false
                                        ? '<strong>✓ Video + HLS đã lên cloud.</strong> <span style="color:#f59e0b">Chưa xóa được video gốc trong storage Laravel; hãy kiểm tra quyền ghi/xóa của thư mục storage.</span>'
                                        : '<strong>✓ Video + HLS đã lên cloud, video gốc đã được xóa khỏi storage Laravel.</strong>';
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

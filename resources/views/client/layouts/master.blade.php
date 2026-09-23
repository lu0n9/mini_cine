<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    @include('client.partials.head')
</head>
<body class="bg-gray-900 text-white min-h-screen flex flex-col">
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    {{-- Header / Navbar dùng chung --}}
    @include('client.partials.header')

    {{-- Nội dung thay đổi theo từng trang --}}
    <main class="flex-grow container mx-auto px-4 py-6">
        @yield('content')
    </main>

    {{-- Footer dùng chung --}}
    @include('client.partials.footer')
</body>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const trailerModal = document.getElementById('trailer');
        const trailerIframe = document.getElementById('trailer-iframe');

        if (!trailerModal || !trailerIframe) {
            return;
        }

        const trailerLinks = document.querySelectorAll(
            '[data-trailer-url]'
        );

        function getYoutubeEmbedUrl(url) {

            try {

                const parsedUrl = new URL(url);

                let videoId = '';

                // youtube.com/watch?v=xxxx
                if (parsedUrl.hostname.includes('youtube.com')) {
                    videoId = parsedUrl.searchParams.get('v');
                }

                // youtu.be/xxxx
                if (parsedUrl.hostname.includes('youtu.be')) {
                    videoId = parsedUrl.pathname.substring(1);
                }

                if (!videoId) {
                    return null;
                }

                return 'https://www.youtube.com/embed/' +
                    videoId +
                    '?autoplay=1&rel=0';

            } catch (error) {

                console.error('Trailer URL không hợp lệ:', error);

                return null;
            }
        }


        trailerLinks.forEach(function (link) {

            link.addEventListener('click', function (event) {

                event.preventDefault();

                const trailerUrl =
                    link.dataset.trailerUrl;

                const embedUrl =
                    getYoutubeEmbedUrl(trailerUrl);

                if (!embedUrl) {
                    alert('Link trailer không hợp lệ.');
                    return;
                }

                trailerIframe.src = embedUrl;

                window.location.hash = 'trailer';

            });

        });


        function closeTrailer() {

            trailerIframe.src = '';

            if (window.location.hash === '#trailer') {
                history.replaceState(
                    null,
                    '',
                    window.location.pathname +
                    window.location.search
                );
            }

        }


        const closeButton =
            trailerModal.querySelector('.modal__close');

        const backdrop =
            trailerModal.querySelector('.modal__backdrop');


        if (closeButton) {

            closeButton.addEventListener(
                'click',
                function () {
                    closeTrailer();
                }
            );

        }


        if (backdrop) {

            backdrop.addEventListener(
                'click',
                function () {
                    closeTrailer();
                }
            );

        }


        document.addEventListener(
            'keydown',
            function (event) {

                if (event.key === 'Escape') {
                    closeTrailer();
                }

            }
        );

    });
</script>
@auth
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('rating-form');

            if (!form) {
                return;
            }

            const submitButton = document.getElementById('rating-submit');
            const message = document.getElementById('rating-message');
            const averageElement = document.getElementById('rating-average');
            const countElement = document.getElementById('rating-count');
            const starsElement = document.getElementById('rating-stars');
            const barsElement = document.getElementById('rating-bars');

            form.addEventListener('submit', async function (event) {
                event.preventDefault();

                const selectedRating = form.querySelector(
                    'input[name="rating"]:checked'
                );

                if (!selectedRating) {
                    showMessage('Vui lòng chọn số sao.', false);
                    return;
                }

                submitButton.disabled = true;
                submitButton.textContent = 'Đang gửi...';

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': form.querySelector(
                                'input[name="_token"]'
                            ).value,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: new FormData(form)
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(
                            data.message || 'Không thể gửi đánh giá.'
                        );
                    }

                    if (data.success) {
                        updateRating(data);
                        showMessage(data.message, true);
                    }
                } catch (error) {
                    showMessage(error.message, false);
                } finally {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Gửi đánh giá';
                }
            });

            function updateRating(data) {
                if (data.rating_average !== undefined) {
                    averageElement.textContent =
                        Number(data.rating_average).toFixed(1);
                }

                if (data.rating_count !== undefined) {
                    countElement.textContent =
                        Number(data.rating_count).toLocaleString('vi-VN');
                }

                if (data.rating_percentages) {
                    updateRatingBars(data.rating_percentages);
                }

                if (data.rating_average !== undefined) {
                    updateRatingStars(
                        Number(data.rating_average)
                    );
                }
            }

            function updateRatingStars(average) {
                const stars = starsElement.querySelectorAll('svg');

                stars.forEach(function (star, index) {
                    const starNumber = index + 1;

                    if (average >= starNumber * 2) {
                        star.classList.remove('off');
                    } else {
                        star.classList.add('off');
                    }
                });

                starsElement.setAttribute(
                    'aria-label',
                    average.toFixed(1) + '/10'
                );
            }

            function updateRatingBars(percentages) {
                const rows = barsElement.querySelectorAll('.rating__row');

                rows.forEach(function (row) {
                    const star = row.querySelector('b')
                        .textContent
                        .trim()
                        .replace(' sao', '');

                    const percentage = percentages[star] ?? 0;

                    const track = row.querySelector('.track span');
                    const percentageElement = row.querySelector('.rating__pct');

                    track.style.width = percentage + '%';
                    percentageElement.textContent = percentage + '%';
                });
            }

            function showMessage(text, success) {
                message.textContent = text;
                message.style.display = 'block';

                if (success) {
                    message.classList.remove('error');
                    message.classList.add('success');
                } else {
                    message.classList.remove('success');
                    message.classList.add('error');
                }
            }
        });
    </script>
@endauth
@auth
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const commentForm = document.getElementById('comment-form');
            const parentInput = document.getElementById('parent-id');
            const textarea = document.getElementById('cmt');
            const cancelReply = document.getElementById('cancel-reply');
            const replyStatus = document.getElementById('reply-status');
            const commentsList = document.getElementById('comments-list');
            const commentsCount = document.getElementById('comments-count');
            const submitButton = document.getElementById('comment-submit');

            if (!commentForm) {
                return;
            }

            function escapeHtml(value) {
                const element = document.createElement('div');

                element.textContent = value ?? '';

                return element.innerHTML;
            }

            function resetReply() {
                parentInput.value = '';
                textarea.placeholder = 'Chia sẻ cảm nhận của bạn về phim…';
                replyStatus.textContent =
                    'Hãy bình luận văn minh, tránh tiết lộ tình tiết quan trọng.';
                cancelReply.style.display = 'none';
            }

            document.addEventListener('click', function (event) {
                const replyButton = event.target.closest('.reply-button');

                if (!replyButton) {
                    return;
                }

                parentInput.value = replyButton.dataset.commentId;

                textarea.placeholder =
                    'Đang trả lời ' +
                    replyButton.dataset.commentName +
                    '...';

                replyStatus.textContent =
                    'Đang trả lời ' +
                    replyButton.dataset.commentName;

                cancelReply.style.display = 'inline-block';

                commentForm.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                textarea.focus();
            });

            cancelReply.addEventListener('click', function () {
                textarea.value = '';
                resetReply();
            });

            commentForm.addEventListener('submit', function (event) {
                event.preventDefault();

                submitButton.disabled = true;
                submitButton.textContent = 'Đang gửi...';

                const formData = new FormData(commentForm);

                fetch(commentForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                    .then(async function (response) {
                        const data = await response.json();

                        if (!response.ok) {
                            throw {
                                message: data.message ?? 'Có lỗi xảy ra.',
                                errors: data.errors ?? {}
                            };
                        }

                        return data;
                    })
                    .then(function (data) {
                        const comment = data.comment;
                        const parentId = comment.parent_id;

                        const commentHtml = `
                            <li
                                class="comment"
                                data-comment-id="${comment.id}"
                            >
                                <img
                                    src="/placeholder-user.jpg"
                                    alt="Ảnh đại diện"
                                    width="40"
                                    height="40"
                                >

                                <div class="comment__body">
                                    <div class="comment__head">
                                        <span class="comment__name">
                                            ${escapeHtml(comment.user_name)}
                                        </span>

                                        <span class="comment__time">
                                            ${escapeHtml(comment.created_at)}
                                        </span>
                                    </div>

                                    <p class="comment__text">
                                        ${escapeHtml(comment.content)}
                                    </p>

                                    <div class="comment__actions">
                                        <button
                                            type="button"
                                            class="comment-like-button"
                                            data-comment-id="${comment.id}"
                                        >
                                            <span class="like-icon">👍</span>
                                            <span class="like-count">0</span>
                                        </button>

                                        <button
                                            type="button"
                                            class="reply-button"
                                            data-comment-id="${comment.id}"
                                            data-comment-name="${escapeHtml(comment.user_name)}"
                                        >
                                            Trả lời
                                        </button>

                                        <button type="button">
                                            Báo cáo
                                        </button>
                                    </div>
                                </div>
                            </li>
                        `;

                        const noComments =
                            document.getElementById('no-comments');

                        if (noComments) {
                            noComments.remove();
                        }

                        if (parentId) {
                            const parentButton = document.querySelector(
                                `.reply-button[data-comment-id="${parentId}"]`
                            );

                            if (parentButton) {
                                const parentBody =
                                    parentButton.closest('.comment__body');

                                parentBody.insertAdjacentHTML(
                                    'beforeend',
                                    commentHtml
                                );
                            }
                        } else {
                            commentsList.insertAdjacentHTML(
                                'afterbegin',
                                commentHtml
                            );

                            const count =
                                parseInt(
                                    commentsCount.textContent.replace(/\D/g, ''),
                                    10
                                ) || 0;

                            commentsCount.textContent =
                                new Intl.NumberFormat('vi-VN')
                                    .format(count + 1);
                        }

                        commentForm.reset();
                        resetReply();

                        replyStatus.textContent = data.message;
                    })
                    .catch(function (error) {
                        if (error.errors?.content) {
                            replyStatus.textContent =
                                error.errors.content[0];

                            return;
                        }

                        replyStatus.textContent =
                            error.message ??
                            'Không thể gửi bình luận.';
                    })
                    .finally(function () {
                        submitButton.disabled = false;
                        submitButton.textContent = 'Gửi bình luận';
                    });
            });

            document.addEventListener('click', function (event) {
                const likeButton = event.target.closest(
                    '.comment-like-button'
                );

                if (!likeButton) {
                    return;
                }

                const commentId = likeButton.dataset.commentId;

                if (!commentId || likeButton.dataset.loading === '1') {
                    return;
                }

                likeButton.dataset.loading = '1';

                fetch(`/comments/${commentId}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(async function (response) {
                        const data = await response.json();

                        if (!response.ok) {
                            throw new Error(
                                data.message ??
                                'Không thể like bình luận.'
                            );
                        }

                        return data;
                    })
                    .then(function (data) {
                        if (!data.success) {
                            return;
                        }

                        likeButton.querySelector('.like-icon').textContent =
                            data.liked ? '❤️' : '👍';

                        likeButton.querySelector('.like-count').textContent =
                            data.likes_count;

                        likeButton.classList.toggle(
                            'liked',
                            data.liked
                        );
                    })
                    .catch(function (error) {
                        console.error('Like error:', error);
                    })
                    .finally(function () {
                        likeButton.dataset.loading = '0';
                    });
            });
        });
    </script>
@endauth
@auth
    <script>
        async function toggleFavorite(button) {
            const url = button.dataset.url;
            const message = document.getElementById('favorite-message');
            const text = button.querySelector('span');

            button.disabled = true;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(
                        data.message || 'Không thể cập nhật danh sách.'
                    );
                }

                const svg = button.querySelector('svg');

                if (data.favorited) {
                    text.textContent = 'Đã lưu';
                    svg.setAttribute('fill', 'currentColor');
                } else {
                    text.textContent = 'Danh sách của tôi';
                    svg.setAttribute('fill', 'none');
                }

                message.textContent = data.message;
                message.style.display = 'block';

                setTimeout(function () {
                    message.style.display = 'none';
                }, 2000);
            } catch (error) {
                message.textContent = error.message;
                message.style.display = 'block';
            } finally {
                button.disabled = false;
            }
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const toggle = document.getElementById('userMenuToggle');
            const dropdown = document.getElementById('userDropdown');

            if (!toggle || !dropdown) {
                return;
            }

            // Click avatar
            toggle.addEventListener('click', function (event) {
                event.stopPropagation();

                const isOpen = dropdown.classList.toggle('show');

                toggle.setAttribute('aria-expanded', isOpen);
            });

            // Click ra bên ngoài -> đóng dropdown
            document.addEventListener('click', function (event) {

                if (!dropdown.contains(event.target) &&
                    !toggle.contains(event.target)) {

                    dropdown.classList.remove('show');
                    toggle.setAttribute('aria-expanded', 'false');
                }

            });

        });
    </script>
@endauth
<script>

    let recommendCurrentStep = 1;

    function openRecommendationModal() {

        recommendCurrentStep = 1;

        document
            .getElementById('recommendModal')
            .classList.add('is-open');

        document.body.style.overflow = 'hidden';

        updateRecommendationStep();
    }


    function closeRecommendationModal() {

        document
            .getElementById('recommendModal')
            .classList.remove('is-open');

        document.body.style.overflow = '';

    }


    function updateRecommendationStep() {

        document
            .querySelectorAll('.recommend-step')
            .forEach(step => {

                step.style.display =
                    Number(step.dataset.step) === recommendCurrentStep
                        ? 'block'
                        : 'none';

            });


        document.getElementById('recommendStepNumber')
            .textContent = recommendCurrentStep;


        document.getElementById('recommendBack')
            .style.display =
                recommendCurrentStep > 1
                    ? 'inline-flex'
                    : 'none';


        document.getElementById('recommendNext')
            .textContent =
                recommendCurrentStep === 3
                    ? '✨ Tìm phim cho tôi'
                    : 'Tiếp tục';
    }


    function recommendNextStep() {

        if (recommendCurrentStep < 3) {

            recommendCurrentStep++;

            updateRecommendationStep();

            return;
        }


        submitRecommendation();
    }


    function recommendPreviousStep() {

        if (recommendCurrentStep > 1) {

            recommendCurrentStep--;

            updateRecommendationStep();

        }

    }


    async function submitRecommendation() {
        // Lấy các genre đã chọn
        const genres = Array.from(
            document.querySelectorAll('input[name="genres[]"]:checked')
        ).map(input => input.value);

        // Lấy các country đã chọn
        const countries = Array.from(
            document.querySelectorAll('input[name="countries[]"]:checked')
        ).map(input => input.value);

        // Lấy các person đã chọn
        const people = Array.from(
            document.querySelectorAll('input[name="people[]"]:checked')
        ).map(input => input.value);

        // Tạo dữ liệu gửi lên Laravel
        const data = {
            genres: genres,
            countries: countries,
            people: people
        };

        try {
            const response = await fetch('/api/recommendations/preferences', {
                method: 'POST',

                credentials: 'same-origin',

                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute('content')
                },

                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(
                    result.message || 'Không thể lưu sở thích.'
                );
            }

            if (result.success) {
                alert(result.message);

                closeRecommendationModal();

                // Có thể reload để cập nhật giao diện recommendation
                window.location.reload();
            }

        } catch (error) {
            console.error('Recommendation error:', error);

            alert(
                error.message || 'Có lỗi xảy ra. Vui lòng thử lại.'
            );
        }
    }

</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        /* =========================================================
        DESKTOP / PC MAIN MENU
        Chỉ xử lý:
        .nav-dropdown
        .nav-dropdown-toggle
        .nav-dropdown-menu
        ========================================================= */

        const desktopDropdowns =
            document.querySelectorAll('.nav-dropdown');


        desktopDropdowns.forEach(function (dropdown) {

            const toggle =
                dropdown.querySelector('.nav-dropdown-toggle');

            const menu =
                dropdown.querySelector('.nav-dropdown-menu');

            if (!toggle || !menu) {
                return;
            }

            let closeTimer = null;


            /* =====================================================
            TÍNH VỊ TRÍ DROPDOWN
            ===================================================== */

            function positionDropdown() {

                const rect =
                    toggle.getBoundingClientRect();

                menu.style.top =
                    (rect.bottom + 7) + 'px';

                menu.style.left =
                    rect.left + 'px';


                requestAnimationFrame(function () {

                    const menuRect =
                        menu.getBoundingClientRect();

                    let left = rect.left;


                    /* Không tràn bên phải */
                    if (
                        left + menuRect.width >
                        window.innerWidth - 10
                    ) {
                        left =
                            window.innerWidth -
                            menuRect.width -
                            10;
                    }


                    /* Không tràn bên trái */
                    if (left < 10) {
                        left = 10;
                    }


                    menu.style.left =
                        left + 'px';

                });
            }


            /* =====================================================
            ĐÓNG DROPDOWN KHÁC
            ===================================================== */

            function closeOtherDropdowns() {

                desktopDropdowns.forEach(function (item) {

                    if (item === dropdown) {
                        return;
                    }


                    item.classList.remove('is-open');


                    const otherMenu =
                        item.querySelector(
                            '.nav-dropdown-menu'
                        );


                    const otherToggle =
                        item.querySelector(
                            '.nav-dropdown-toggle'
                        );


                    if (otherMenu) {

                        otherMenu.classList.remove(
                            'is-visible'
                        );

                    }


                    if (otherToggle) {

                        otherToggle.setAttribute(
                            'aria-expanded',
                            'false'
                        );

                    }

                });

            }


            /* =====================================================
            MỞ DROPDOWN
            ===================================================== */

            function openDropdown() {

                clearTimeout(closeTimer);

                closeOtherDropdowns();

                positionDropdown();


                menu.classList.add(
                    'is-visible'
                );


                dropdown.classList.add(
                    'is-open'
                );


                toggle.setAttribute(
                    'aria-expanded',
                    'true'
                );

            }


            /* =====================================================
            ĐÓNG DROPDOWN
            ===================================================== */

            function closeDropdown() {

                clearTimeout(closeTimer);


                closeTimer = setTimeout(function () {

                    menu.classList.remove(
                        'is-visible'
                    );


                    dropdown.classList.remove(
                        'is-open'
                    );


                    toggle.setAttribute(
                        'aria-expanded',
                        'false'
                    );

                }, 120);

            }


            /* =====================================================
            DESKTOP: HOVER
            ===================================================== */

            dropdown.addEventListener(
                'mouseenter',
                function () {

                    if (window.innerWidth > 768) {
                        openDropdown();
                    }

                }
            );


            dropdown.addEventListener(
                'mouseleave',
                function () {

                    if (window.innerWidth > 768) {
                        closeDropdown();
                    }

                }
            );


            /* =====================================================
            DESKTOP: CLICK
            ===================================================== */

            toggle.addEventListener(
                'click',
                function (event) {

                    if (window.innerWidth <= 768) {
                        return;
                    }


                    event.preventDefault();


                    if (
                        dropdown.classList.contains(
                            'is-open'
                        )
                    ) {

                        closeDropdown();

                    } else {

                        openDropdown();

                    }

                }
            );


            /* =====================================================
            MOBILE KHÔNG CÒN XỬ LÝ Ở ĐÂY
            ===================================================== */

        });



        /* =========================================================
        DESKTOP: CLICK RA NGOÀI
        ========================================================= */

        document.addEventListener(
            'click',
            function (event) {

                if (window.innerWidth <= 768) {
                    return;
                }


                if (
                    !event.target.closest('.nav-dropdown')
                ) {

                    desktopDropdowns.forEach(
                        function (dropdown) {

                            const menu =
                                dropdown.querySelector(
                                    '.nav-dropdown-menu'
                                );


                            const toggle =
                                dropdown.querySelector(
                                    '.nav-dropdown-toggle'
                                );


                            dropdown.classList.remove(
                                'is-open'
                            );


                            if (menu) {

                                menu.classList.remove(
                                    'is-visible'
                                );

                            }


                            if (toggle) {

                                toggle.setAttribute(
                                    'aria-expanded',
                                    'false'
                                );

                            }

                        }
                    );

                }

            }
        );



        /* =========================================================
        DESKTOP: RESIZE
        ========================================================= */

        function repositionDesktopDropdown() {

            if (window.innerWidth <= 768) {
                return;
            }


            const dropdown =
                document.querySelector(
                    '.nav-dropdown.is-open'
                );


            if (!dropdown) {
                return;
            }


            const toggle =
                dropdown.querySelector(
                    '.nav-dropdown-toggle'
                );


            const menu =
                dropdown.querySelector(
                    '.nav-dropdown-menu'
                );


            if (!toggle || !menu) {
                return;
            }


            const rect =
                toggle.getBoundingClientRect();


            menu.style.top =
                (rect.bottom + 7) + 'px';


            let left = rect.left;


            const menuRect =
                menu.getBoundingClientRect();


            if (
                left + menuRect.width >
                window.innerWidth - 10
            ) {

                left =
                    window.innerWidth -
                    menuRect.width -
                    10;

            }


            if (left < 10) {
                left = 10;
            }


            menu.style.left =
                left + 'px';

        }


        window.addEventListener(
            'resize',
            repositionDesktopDropdown
        );


        window.addEventListener(
            'scroll',
            repositionDesktopDropdown,
            true
        );



        /* =========================================================
        MOBILE MAIN MENU
        
        Chỉ xử lý:
        .mobile-nav-dropdown
        .mobile-nav-toggle
        .mobile-nav-menu
        
        Không đụng vào .nav-dropdown
        ========================================================= */

        const mobileDropdowns =
            document.querySelectorAll(
                '.mobile-nav-dropdown'
            );


        mobileDropdowns.forEach(
            function (dropdown) {

                const toggle =
                    dropdown.querySelector(
                        '.mobile-nav-toggle'
                    );


                const menu =
                    dropdown.querySelector(
                        '.mobile-nav-menu'
                    );


                if (!toggle || !menu) {
                    return;
                }


                /* =================================================
                ĐÓNG MENU MOBILE
                ================================================= */

                function closeMobileDropdown() {

                    dropdown.classList.remove(
                        'is-open'
                    );


                    toggle.setAttribute(
                        'aria-expanded',
                        'false'
                    );

                }


                /* =================================================
                MỞ MENU MOBILE
                ================================================= */

                function openMobileDropdown() {

                    /* Đóng menu mobile khác */

                    mobileDropdowns.forEach(
                        function (item) {

                            if (item === dropdown) {
                                return;
                            }


                            item.classList.remove(
                                'is-open'
                            );


                            const otherToggle =
                                item.querySelector(
                                    '.mobile-nav-toggle'
                                );


                            if (otherToggle) {

                                otherToggle.setAttribute(
                                    'aria-expanded',
                                    'false'
                                );

                            }

                        }
                    );


                    dropdown.classList.add(
                        'is-open'
                    );


                    toggle.setAttribute(
                        'aria-expanded',
                        'true'
                    );

                }


                /* =================================================
                MOBILE: CLICK
                ================================================= */

                toggle.addEventListener(
                    'click',
                    function (event) {

                        event.preventDefault();

                        event.stopPropagation();


                        if (
                            dropdown.classList.contains(
                                'is-open'
                            )
                        ) {

                            closeMobileDropdown();

                        } else {

                            openMobileDropdown();

                        }

                    }
                );

            }
        );



        /* =========================================================
        MOBILE: CLICK RA NGOÀI
        ========================================================= */

        document.addEventListener(
            'click',
            function (event) {

                if (window.innerWidth > 768) {
                    return;
                }


                if (
                    !event.target.closest(
                        '.mobile-nav-dropdown'
                    )
                ) {

                    mobileDropdowns.forEach(
                        function (dropdown) {

                            dropdown.classList.remove(
                                'is-open'
                            );


                            const toggle =
                                dropdown.querySelector(
                                    '.mobile-nav-toggle'
                                );


                            if (toggle) {

                                toggle.setAttribute(
                                    'aria-expanded',
                                    'false'
                                );

                            }

                        }
                    );

                }

            }
        );

    });
</script>
</html>
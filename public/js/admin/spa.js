/**
 * CineAdmin Single Page Application (SPA) Router
 * Chuyển trang mượt mà không reload trang web khi bấm ở sidebar admin hoặc phân trang
 */
(function () {
    'use strict';

    // Khởi tạo thanh tiến trình (progress bar)
    let progressBar = null;

    function getProgressBar() {
        if (!progressBar) {
            progressBar = document.getElementById('adminProgressBar');
            if (!progressBar) {
                progressBar = document.createElement('div');
                progressBar.id = 'adminProgressBar';
                progressBar.className = 'admin-progress-bar';
                document.body.prepend(progressBar);
            }
        }
        return progressBar;
    }

    let progressTimer = null;
    let currentAbortController = null;
    let isNavigating = false;

    function startProgress() {
        const bar = getProgressBar();
        if (progressTimer) clearInterval(progressTimer);

        bar.style.transition = 'none';
        bar.style.width = '0%';
        bar.className = 'admin-progress-bar active';

        // Force reflow
        void bar.offsetWidth;

        bar.style.transition = 'width 0.25s ease, opacity 0.25s ease';
        bar.style.width = '25%';

        let currentWidth = 25;
        progressTimer = setInterval(() => {
            if (currentWidth < 80) {
                currentWidth += (85 - currentWidth) * 0.12;
                bar.style.width = currentWidth + '%';
            }
        }, 120);
    }

    function finishProgress() {
        const bar = getProgressBar();
        if (progressTimer) clearInterval(progressTimer);

        bar.style.transition = 'width 0.15s ease, opacity 0.25s ease';
        bar.style.width = '100%';

        setTimeout(() => {
            bar.classList.add('done');
            setTimeout(() => {
                bar.className = 'admin-progress-bar';
                bar.style.width = '0%';
            }, 300);
        }, 150);
    }

    function resetProgress() {
        const bar = getProgressBar();
        if (progressTimer) clearInterval(progressTimer);
        bar.className = 'admin-progress-bar';
        bar.style.width = '0%';
    }

    // Chuẩn hóa URL để so sánh
    function normalizeUrl(url) {
        try {
            const u = new URL(url, window.location.origin);
            return (u.origin + u.pathname.replace(/\/$/, '') + (u.search || '')).toLowerCase();
        } catch (e) {
            return (url || '').toLowerCase();
        }
    }

    // Cập nhật trạng thái active trên sidebar
    function updateSidebarActive(currentUrl) {
        const sidebar = document.getElementById('adminSidebar');
        if (!sidebar) return;

        let targetUrl = currentUrl || window.location.href;
        let targetObj;
        try {
            targetObj = new URL(targetUrl, window.location.origin);
        } catch (e) {
            return;
        }

        const targetNormalized = normalizeUrl(targetUrl);
        const targetPath = targetObj.pathname.replace(/\/$/, '').toLowerCase();

        const navLinks = sidebar.querySelectorAll('a.nav-item');
        let bestMatch = null;
        let bestScore = -1;

        navLinks.forEach(link => {
            link.classList.remove('active');

            let linkObj;
            try {
                linkObj = new URL(link.href, window.location.origin);
            } catch (e) {
                return;
            }

            const linkNormalized = normalizeUrl(link.href);
            const linkPath = linkObj.pathname.replace(/\/$/, '').toLowerCase();

            // 1. Khớp chính xác cả query string
            if (linkNormalized === targetNormalized) {
                bestMatch = link;
                bestScore = 100;
            }
            // 2. Khớp chính xác pathname
            else if (bestScore < 80 && linkPath === targetPath) {
                bestMatch = link;
                bestScore = 80;
            }
            // 3. Khớp tiền tố (ví dụ /admin/movies/create khớp với /admin/movies nếu không có link create)
            else if (bestScore < 50 && targetPath.startsWith(linkPath) && linkPath !== '/admin' && linkPath !== '') {
                bestMatch = link;
                bestScore = 50;
            }
        });

        if (bestMatch) {
            bestMatch.classList.add('active');

            // Mở details.group chứa link này
            const group = bestMatch.closest('details.group');
            if (group) {
                group.open = true;
            }
        }
    }

    // Cập nhật số badge trên sidebar nếu trang mới có dữ liệu mới
    function updateSidebarBadges(newDoc) {
        const currentSidebar = document.getElementById('adminSidebar');
        const newSidebar = newDoc.getElementById('adminSidebar');
        if (!currentSidebar || !newSidebar) return;

        const newLinks = newSidebar.querySelectorAll('a.nav-item');
        const newMap = new Map();

        newLinks.forEach(link => {
            try {
                const path = new URL(link.href, window.location.origin).pathname;
                newMap.set(path, link);
            } catch (e) {}
        });

        const currentLinks = currentSidebar.querySelectorAll('a.nav-item');
        currentLinks.forEach(currLink => {
            try {
                const path = new URL(currLink.href, window.location.origin).pathname;
                const newLink = newMap.get(path);
                if (newLink) {
                    const newBadge = newLink.querySelector('.badge');
                    let currBadge = currLink.querySelector('.badge');

                    if (newBadge) {
                        if (currBadge) {
                            currBadge.textContent = newBadge.textContent;
                            currBadge.className = newBadge.className;
                        } else {
                            currLink.appendChild(newBadge.cloneNode(true));
                        }
                    } else if (currBadge) {
                        currBadge.remove();
                    }
                }
            } catch (e) {}
        });
    }

    // Tự động đóng sidebar trên màn hình mobile khi đã chọn trang
    function closeMobileSidebar() {
        const app = document.querySelector('.app');
        if (window.innerWidth <= 760 && app) {
            app.classList.remove('mobile-sidebar-open');
            document.body.classList.remove('mobile-sidebar-active');
        }
    }

    // Nạp thêm file CSS nếu trang đích có CSS riêng chưa có trong head
    function updateStylesheets(newDoc) {
        const newLinks = newDoc.querySelectorAll('link[rel="stylesheet"]');
        newLinks.forEach(newLink => {
            const href = newLink.getAttribute('href');
            if (href && !document.querySelector(`link[rel="stylesheet"][href="${href}"]`)) {
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = href;
                document.head.appendChild(link);
            }
        });
    }

    // Thực thi tuần tự các thẻ <script> bên trong container mới
    async function executeScripts(container) {
        const scripts = Array.from(container.querySelectorAll('script'));

        for (const oldScript of scripts) {
            // Xóa script cũ khỏi DOM trước khi thực thi
            if (oldScript.parentNode) {
                oldScript.parentNode.removeChild(oldScript);
            }

            if (oldScript.src) {
                // Script ngoài (CDN / external file)
                const alreadyExists = document.querySelector(`script[src="${oldScript.src}"]`);
                if (!alreadyExists) {
                    await new Promise(resolve => {
                        const s = document.createElement('script');
                        Array.from(oldScript.attributes).forEach(attr => s.setAttribute(attr.name, attr.value));
                        s.onload = resolve;
                        s.onerror = resolve;
                        document.head.appendChild(s);
                    });
                }
            } else {
                // Script nội bộ (inline)
                // Proxy addEventListener để các hàm đăng ký DOMContentLoaded hoặc load chạy ngay lập tức
                const originalDocAddEventListener = document.addEventListener;
                const originalWinAddEventListener = window.addEventListener;

                const runImmediately = function (target, originalFn, type, listener, options) {
                    if (type === 'DOMContentLoaded' || type === 'load') {
                        try {
                            if (typeof listener === 'function') {
                                listener.call(target, new Event(type));
                            } else if (listener && typeof listener.handleEvent === 'function') {
                                listener.handleEvent(new Event(type));
                            }
                        } catch (err) {
                            console.error('Error in SPA page script:', err);
                        }
                        return;
                    }
                    return originalFn.call(target, type, listener, options);
                };

                document.addEventListener = function (type, listener, options) {
                    return runImmediately(document, originalDocAddEventListener, type, listener, options);
                };

                window.addEventListener = function (type, listener, options) {
                    return runImmediately(window, originalWinAddEventListener, type, listener, options);
                };

                try {
                    const newScript = document.createElement('script');
                    Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                    newScript.textContent = oldScript.textContent;
                    document.body.appendChild(newScript);
                    newScript.remove();
                } catch (e) {
                    console.error('Error executing inline script:', e);
                } finally {
                    document.addEventListener = originalDocAddEventListener;
                    window.addEventListener = originalWinAddEventListener;
                }
            }
        }
    }

    // Tải và hiển thị trang qua AJAX
    async function loadPage(url, pushState = true) {
        if (isNavigating && currentAbortController) {
            currentAbortController.abort();
        }

        let targetUrl;
        try {
            targetUrl = new URL(url, window.location.origin).href;
        } catch (e) {
            window.location.href = url;
            return;
        }

        // Nếu bấm đúng URL hiện tại, chỉ cuộn mượt lên đầu
        if (normalizeUrl(targetUrl) === normalizeUrl(window.location.href) && pushState) {
            window.scrollTo({ top: 0, behavior: 'smooth' });
            return;
        }

        currentAbortController = new AbortController();
        isNavigating = true;

        const contentEl = document.querySelector('main.content');
        if (contentEl) {
            contentEl.classList.add('is-loading');
        }
        startProgress();

        try {
            const response = await fetch(targetUrl, {
                signal: currentAbortController.signal,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-Admin-SPA': 'true'
                }
            });

            // Nếu server redirect (vd: hết hạn phiên chuyển về login)
            if (response.redirected) {
                window.location.href = response.url;
                return;
            }

            // Nếu có lỗi HTTP (404, 500...), tải lại toàn trang chuẩn
            if (!response.ok) {
                window.location.href = targetUrl;
                return;
            }

            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const newContent = doc.querySelector('main.content');
            if (!newContent) {
                // Không tìm thấy container main.content (không phải layout admin)
                window.location.href = targetUrl;
                return;
            }

            // Cập nhật tiêu đề tab trình duyệt
            if (doc.title) {
                document.title = doc.title;
            }

            // Cập nhật URL trình duyệt (history)
            if (pushState) {
                window.history.pushState({ spa: true, url: targetUrl }, '', targetUrl);
            }

            // Nạp thêm file CSS nếu cần
            updateStylesheets(doc);

            // Thay thế nội dung phần content
            if (contentEl) {
                contentEl.innerHTML = newContent.innerHTML;
                contentEl.className = newContent.className;
                contentEl.classList.remove('is-loading');
            }

            // Cập nhật active và badge trên sidebar
            updateSidebarActive(targetUrl);
            updateSidebarBadges(doc);

            // Tự động đóng sidebar trên mobile
            closeMobileSidebar();

            // Cuộn lên đầu trang
            window.scrollTo({ top: 0, behavior: 'instant' });
            if (contentEl) {
                contentEl.scrollTop = 0;
            }

            // Thực thi các script trong nội dung mới
            if (contentEl) {
                await executeScripts(contentEl);
            }

            // Tái kích hoạt các component dùng chung (checkbox, menu, modal)
            if (typeof window.initAdminPermissionCheckboxes === 'function') {
                window.initAdminPermissionCheckboxes();
            }
            if (typeof window.initAdminMenuToggles === 'function') {
                window.initAdminMenuToggles();
            }

            // Kích hoạt sự kiện tùy chỉnh admin:page-loaded
            window.dispatchEvent(new CustomEvent('admin:page-loaded', {
                detail: { url: targetUrl, title: doc.title }
            }));

            finishProgress();
        } catch (err) {
            if (err.name === 'AbortError') {
                // Người dùng đã bấm sang link khác trước khi link này kịp tải xong, bỏ qua
                return;
            }
            console.error('SPA navigation error, falling back to full reload:', err);
            resetProgress();
            window.location.href = targetUrl;
        } finally {
            isNavigating = false;
            if (contentEl) {
                contentEl.classList.remove('is-loading');
            }
        }
    }

    // Bắt sự kiện click trên toàn trang
    document.addEventListener('click', function (e) {
        // Chỉ xử lý click chuột trái và không giữ các phím Ctrl/Cmd/Shift/Alt
        if (e.button !== 0 || e.ctrlKey || e.metaKey || e.altKey || e.shiftKey) {
            return;
        }

        const link = e.target.closest('a');
        if (!link) return;

        // Bỏ qua các liên kết mở tab mới hoặc tải file
        if (link.getAttribute('target') === '_blank' || link.hasAttribute('download')) {
            return;
        }

        // Bỏ qua nếu có data-no-spa
        if (link.dataset.noSpa === 'true' || link.dataset.noSpa === '') {
            return;
        }

        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript:')) {
            return;
        }

        // Kiểm tra domain (chỉ hỗ trợ same-origin)
        let urlObj;
        try {
            urlObj = new URL(link.href, window.location.origin);
        } catch (err) {
            return;
        }

        if (urlObj.origin !== window.location.origin) {
            return;
        }

        // Bỏ qua link logout
        if (urlObj.pathname === '/admin/logout' || urlObj.pathname.startsWith('/logout')) {
            return;
        }

        // Kiểm tra xem click xuất phát từ sidebar hay phân trang
        const isSidebarLink = !!link.closest('#adminSidebar');
        const isPaginationLink = !!link.closest('.pagination, .mini-pagination');
        const isSpaExplicit = link.hasAttribute('data-spa');

        // Bấm ở sidebar admin hoặc phân trang -> chuyển trang SPA không reload
        if (isSidebarLink || isPaginationLink || isSpaExplicit) {
            e.preventDefault();
            loadPage(link.href, true);
        }
    });

    // Lắng nghe sự kiện Back/Forward của trình duyệt
    window.addEventListener('popstate', function () {
        loadPage(window.location.href, false);
    });

    // Khởi tạo trạng thái active khi trang được mở lần đầu
    function initOnReady() {
        updateSidebarActive(window.location.href);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initOnReady);
    } else {
        initOnReady();
    }

    // Xuất ra global để có thể gọi thủ công khi cần
    window.AdminSPA = {
        loadPage,
        updateSidebarActive,
        getProgressBar
    };
})();

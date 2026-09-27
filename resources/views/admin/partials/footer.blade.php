<footer class="foot">
  <span>© 2026 CineAdmin — Hệ thống quản lý web xem phim.</span>
  <span>Phiên bản 2.0</span>
  <script>
    document.addEventListener('DOMContentLoaded', function () {

        const groups = document.querySelectorAll('.group');

        // Khôi phục trạng thái menu
        groups.forEach((group, index) => {
            const key = 'admin_menu_group_' + index;
            const savedState = localStorage.getItem(key);

            if (savedState === 'open') {
                group.open = true;
            } else if (savedState === 'closed') {
                group.open = false;
            }
        });

        // Lưu trạng thái khi click mở/đóng
        groups.forEach((group, index) => {
            const key = 'admin_menu_group_' + index;

            group.addEventListener('toggle', function () {
                localStorage.setItem(
                    key,
                    group.open ? 'open' : 'closed'
                );
            });
        });

    });
  </script>
  <!-- // xo nut dang xuat admin -->
  <script>
    const userMenuToggle = document.getElementById('userMenuToggle');
    const logoutDropdown = document.getElementById('logoutDropdown');
    const adminProfileOpen = document.getElementById('adminProfileOpen');
    const adminProfileModal = document.getElementById('adminProfileModal');
    const adminAvatarInput = document.getElementById('adminAvatarInput');
    const adminAvatarPreview = document.getElementById('adminAvatarPreview');

    if (userMenuToggle && logoutDropdown) {
        userMenuToggle.addEventListener('click', function (event) {
            if (event.target.closest('.dropdown-menu')) return;
            event.stopPropagation();
            const isOpen = logoutDropdown.classList.toggle('active');
            userMenuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        userMenuToggle.addEventListener('keydown', function (event) {
            if (event.target !== userMenuToggle || !['Enter', ' '].includes(event.key)) return;
            event.preventDefault();
            userMenuToggle.click();
        });

        document.addEventListener('click', function () {
            logoutDropdown.classList.remove('active');
            userMenuToggle.setAttribute('aria-expanded', 'false');
        });
    }

    function closeAdminProfileModal() {
        if (!adminProfileModal) return;
        adminProfileModal.style.display = 'none';
        document.body.classList.remove('admin-profile-open');
    }

    function openAdminProfileModal() {
        if (!adminProfileModal) return;
        logoutDropdown?.classList.remove('active');
        userMenuToggle?.setAttribute('aria-expanded', 'false');
        adminProfileModal.style.display = 'flex';
        document.body.classList.add('admin-profile-open');
        adminProfileModal.querySelector('input[name="name"]')?.focus();
    }

    adminProfileOpen?.addEventListener('click', function (event) {
        event.stopPropagation();
        openAdminProfileModal();
    });
    adminProfileModal?.querySelectorAll('[data-profile-close]').forEach(button => {
        button.addEventListener('click', closeAdminProfileModal);
    });
    adminProfileModal?.querySelector('.admin-profile-dialog')?.addEventListener('click', event => event.stopPropagation());
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') closeAdminProfileModal();
    });
    adminAvatarInput?.addEventListener('change', function () {
        const file = this.files?.[0];
        if (!file || !adminAvatarPreview) return;
        adminAvatarPreview.src = URL.createObjectURL(file);
    });

    @if (old('profile_submission') || session('profile_success'))
        openAdminProfileModal();
    @endif
  </script>

  {{-- =========================================================
      SIDEBAR COLLAPSE / EXPAND JS
  ========================================================= --}}
  <script>
    document.addEventListener('DOMContentLoaded', function () {
        const app = document.querySelector('.app');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarCollapseBtn = document.getElementById('sidebarCollapseBtn');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function isMobile() {
            return window.innerWidth <= 760;
        }

        // Khôi phục trạng thái sidebar từ localStorage
        function initSidebarState() {
            if (!app) return;
            if (isMobile()) {
                app.classList.remove('sidebar-collapsed');
                document.documentElement.classList.remove('sidebar-is-collapsed');
            } else {
                const isCollapsed = localStorage.getItem('admin_sidebar_collapsed') === 'true';
                if (isCollapsed) {
                    app.classList.add('sidebar-collapsed');
                    document.documentElement.classList.add('sidebar-is-collapsed');
                } else {
                    app.classList.remove('sidebar-collapsed');
                    document.documentElement.classList.remove('sidebar-is-collapsed');
                }
            }
        }

        initSidebarState();

        function toggleSidebar() {
            if (!app) return;
            if (isMobile()) {
                const isOpen = app.classList.toggle('mobile-sidebar-open');
                document.body.classList.toggle('mobile-sidebar-active', isOpen);
            } else {
                const isCollapsed = app.classList.toggle('sidebar-collapsed');
                document.documentElement.classList.toggle('sidebar-is-collapsed', isCollapsed);
                localStorage.setItem('admin_sidebar_collapsed', isCollapsed ? 'true' : 'false');
            }
        }

        function closeSidebar() {
            if (!app) return;
            if (isMobile()) {
                app.classList.remove('mobile-sidebar-open');
                document.body.classList.remove('mobile-sidebar-active');
            } else {
                app.classList.add('sidebar-collapsed');
                document.documentElement.classList.add('sidebar-is-collapsed');
                localStorage.setItem('admin_sidebar_collapsed', 'true');
            }
        }

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function (e) {
                e.preventDefault();
                toggleSidebar();
            });
        }

        if (sidebarCollapseBtn) {
            sidebarCollapseBtn.addEventListener('click', function (e) {
                e.preventDefault();
                closeSidebar();
            });
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function () {
                closeSidebar();
            });
        }

        // Phím tắt Ctrl+B hoặc Cmd+B
        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'b') {
                e.preventDefault();
                toggleSidebar();
            } else if (e.key === 'Escape' && isMobile() && app && app.classList.contains('mobile-sidebar-open')) {
                closeSidebar();
            }
        });

        // Xử lý khi resize màn hình
        window.addEventListener('resize', function () {
            if (!app) return;
            if (!isMobile()) {
                app.classList.remove('mobile-sidebar-open');
                document.body.classList.remove('mobile-sidebar-active');
                const isCollapsed = localStorage.getItem('admin_sidebar_collapsed') === 'true';
                if (isCollapsed) {
                    app.classList.add('sidebar-collapsed');
                    document.documentElement.classList.add('sidebar-is-collapsed');
                } else {
                    app.classList.remove('sidebar-collapsed');
                    document.documentElement.classList.remove('sidebar-is-collapsed');
                }
            }
        });
    });
  </script>


    {{-- ========================================= --}}
    {{-- BAN MODAL SCRIPT --}}
    {{-- ========================================= --}}

    <script>

        function setBanReason() {
            const reasonType = document.getElementById('reason_type').value;
            const reason = document.getElementById('reason');

            if (reasonType && reasonType !== 'Khác') {
                reason.value = reasonType;
            } else if (reasonType === 'Khác') {
                reason.value = '';
                reason.focus();
            }
        }
        function openBanModal(userId, userName, userEmail) {

            const modal = document.getElementById('banModal');
            const form = document.getElementById('banForm');

            const name = document.getElementById('banUserName');
            const email = document.getElementById('banUserEmail');

            const duration = document.getElementById('duration');
            const reason = document.getElementById('reason');

            const submitButton = document.getElementById('banSubmitBtn');
            const cancelButton = document.getElementById('banCancelBtn');
            const loading = document.getElementById('banLoading');


            // Thông tin user
            name.textContent = userName;
            email.textContent = userEmail;


            // Action
            form.action = '/admin/users/' + userId + '/ban';


            // Giá trị mặc định
            duration.value = '7_days';

            reason.value = 'Vi phạm nội quy cộng đồng';


            // Reset trạng thái submit
            form.dataset.submitted = 'false';

            submitButton.disabled = false;
            submitButton.innerHTML = '🔒 Ban User';

            cancelButton.disabled = false;

            loading.style.display = 'none';


            // Hiển thị modal
            modal.style.display = 'flex';

            document.body.style.overflow = 'hidden';
        }


        function closeBanModal() {

            const form = document.getElementById('banForm');

            // Không cho đóng trong lúc đang submit
            if (form.dataset.submitted === 'true') {
                return;
            }

            const modal = document.getElementById('banModal');

            modal.style.display = 'none';

            document.body.style.overflow = '';
        }




        document.addEventListener('keydown', function (event) {

            if (event.key === 'Escape') {
                closeBanModal();
            }

        });

    </script>

    <script>
    window.initAdminPermissionCheckboxes = function () {
        const checkAll = document.getElementById('checkAll');
        const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
        const moduleCheckboxes = document.querySelectorAll('.module-check-all');

        if (!checkAll && permissionCheckboxes.length === 0 && moduleCheckboxes.length === 0) {
            return;
        }

        function updateModuleCheckbox(module) {
            const modulePermissions = document.querySelectorAll('.module-' + module);
            const moduleChecked = document.querySelectorAll('.module-' + module + ':checked');
            const moduleCheckbox = document.querySelector('.module-check-all[data-module="' + module + '"]');

            if (moduleCheckbox) {
                moduleCheckbox.checked = modulePermissions.length > 0 && modulePermissions.length === moduleChecked.length;
            }
        }

        function updateCheckAll() {
            if (!checkAll) return;
            const checked = document.querySelectorAll('.permission-checkbox:checked');
            checkAll.checked = permissionCheckboxes.length > 0 && permissionCheckboxes.length === checked.length;
        }

        checkAll?.addEventListener('change', function () {
            permissionCheckboxes.forEach(function (checkbox) {
                checkbox.checked = checkAll.checked;
            });
            moduleCheckboxes.forEach(function (checkbox) {
                checkbox.checked = checkAll.checked;
            });
        });

        moduleCheckboxes.forEach(function (moduleCheckbox) {
            moduleCheckbox.addEventListener('change', function () {
                const module = moduleCheckbox.dataset.module;
                document.querySelectorAll('.module-' + module).forEach(function (checkbox) {
                    checkbox.checked = moduleCheckbox.checked;
                });
                updateCheckAll();
            });
        });

        permissionCheckboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                updateModuleCheckbox(checkbox.dataset.module);
                updateCheckAll();
            });
        });

        // Đồng bộ trạng thái checkbox khi mở
        moduleCheckboxes.forEach(function (checkbox) {
            updateModuleCheckbox(checkbox.dataset.module);
        });
        updateCheckAll();
    };

    document.addEventListener('DOMContentLoaded', window.initAdminPermissionCheckboxes);
    </script>

    {{-- =========================================================
        MENU TOGGLE JS
    ========================================================= --}}
    <script>
    window.initAdminMenuToggles = function () {
        document.querySelectorAll('.menu-toggle').forEach(function (button) {
            if (button.dataset.bound) return;
            button.dataset.bound = 'true';

            button.addEventListener('click', function () {
                const menuRow = button.closest('.menu-row');
                if (!menuRow) return;

                const children = menuRow.querySelector('.menu-children');
                if (!children) return;

                const isOpen = button.getAttribute('aria-expanded') === 'true';
                button.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
                children.classList.toggle('is-open', !isOpen);
            });
        });
    };

    document.addEventListener('DOMContentLoaded', window.initAdminMenuToggles);
    </script>
</footer>

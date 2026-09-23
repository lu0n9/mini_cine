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

    // Bấm vào sidebar-foot thì ẩn/hiện menu
    userMenuToggle.addEventListener('click', function(e) {
    e.stopPropagation(); // Ngăn sự kiện nổi bọt
    logoutDropdown.classList.toggle('active');
    });

    // Bấm ra bất kỳ đâu ngoài màn hình thì ẩn menu đi
    document.addEventListener('click', function() {
    if (logoutDropdown.classList.contains('active')) {
        logoutDropdown.classList.remove('active');
    }
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
    document.addEventListener('DOMContentLoaded', function () {
        const checkAll = document.getElementById('checkAll');
        const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
        const moduleCheckboxes = document.querySelectorAll('.module-check-all');

        /* CHỌN TẤT CẢ */
        checkAll?.addEventListener('change', function () {
            permissionCheckboxes.forEach(function (checkbox) {
                checkbox.checked = checkAll.checked;
            });
            moduleCheckboxes.forEach(function (checkbox) {
                checkbox.checked = checkAll.checked;
            });
        });

        /* CHỌN THEO MODULE */
        moduleCheckboxes.forEach(function (moduleCheckbox) {
            moduleCheckbox.addEventListener('change', function () {
                const module = moduleCheckbox.dataset.module;
                document.querySelectorAll('.module-' + module).forEach(function (checkbox) {
                    checkbox.checked = moduleCheckbox.checked;
                });
                updateCheckAll();
            });
        });

        /* KHI CHECK TỪNG QUYỀN */
        permissionCheckboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                updateModuleCheckbox(checkbox.dataset.module);
                updateCheckAll();
            });
        });

        function updateModuleCheckbox(module) {
            const modulePermissions = document.querySelectorAll('.module-' + module);
            const moduleChecked = document.querySelectorAll('.module-' + module + ':checked');
            const moduleCheckbox = document.querySelector('.module-check-all[data-module="' + module + '"]');

            if (moduleCheckbox) {
                moduleCheckbox.checked = modulePermissions.length === moduleChecked.length;
            }
        }

        function updateCheckAll() {
            const checked = document.querySelectorAll('.permission-checkbox:checked');
            checkAll.checked = permissionCheckboxes.length === checked.length;
        }
    });
</script>
<script>

    document.addEventListener('DOMContentLoaded', function () {

        const checkAll =
            document.getElementById('checkAll');

        const permissionCheckboxes =
            document.querySelectorAll(
                '.permission-checkbox'
            );

        const moduleCheckboxes =
            document.querySelectorAll(
                '.module-check-all'
            );


        function updateModuleCheckbox(module) {

            const permissions =
                document.querySelectorAll(
                    '.module-' + module
                );

            const checked =
                document.querySelectorAll(
                    '.module-' + module + ':checked'
                );

            const moduleCheckbox =
                document.querySelector(
                    '.module-check-all[data-module="' +
                    module +
                    '"]'
                );

            if (moduleCheckbox) {

                moduleCheckbox.checked =
                    permissions.length > 0 &&
                    permissions.length === checked.length;

            }

        }


        function updateCheckAll() {

            if (!checkAll) {
                return;
            }

            const checked =
                document.querySelectorAll(
                    '.permission-checkbox:checked'
                );

            checkAll.checked =
                permissionCheckboxes.length > 0 &&
                permissionCheckboxes.length === checked.length;

        }


        checkAll?.addEventListener('change', function () {

            permissionCheckboxes.forEach(function (checkbox) {

                checkbox.checked =
                    checkAll.checked;

            });


            moduleCheckboxes.forEach(function (checkbox) {

                checkbox.checked =
                    checkAll.checked;

            });

        });


        moduleCheckboxes.forEach(function (moduleCheckbox) {

            moduleCheckbox.addEventListener(
                'change',
                function () {

                    const module =
                        moduleCheckbox.dataset.module;

                    document
                        .querySelectorAll(
                            '.module-' + module
                        )
                        .forEach(function (checkbox) {

                            checkbox.checked =
                                moduleCheckbox.checked;

                        });


                    updateCheckAll();

                }
            );

        });


        permissionCheckboxes.forEach(function (checkbox) {

            checkbox.addEventListener(
                'change',
                function () {

                    updateModuleCheckbox(
                        checkbox.dataset.module
                    );

                    updateCheckAll();

                }
            );

        });


        /*
        * Đồng bộ trạng thái checkbox
        * ngay khi mở trang Edit.
        */

        moduleCheckboxes.forEach(function (checkbox) {

            updateModuleCheckbox(
                checkbox.dataset.module
            );

        });

        updateCheckAll();

    });

</script>

{{-- =========================================================
    MENU TOGGLE JS
========================================================= --}}
<script>

    document.addEventListener('DOMContentLoaded', function () {

        document.querySelectorAll('.menu-toggle').forEach(function (button) {

            button.addEventListener('click', function () {

                const menuRow =
                    button.closest('.menu-row');

                if (!menuRow) {
                    return;
                }


                const children =
                    menuRow.querySelector('.menu-children');

                if (!children) {
                    return;
                }


                const isOpen =
                    button.getAttribute('aria-expanded') === 'true';


                button.setAttribute(
                    'aria-expanded',
                    isOpen ? 'false' : 'true'
                );


                children.classList.toggle(
                    'is-open',
                    !isOpen
                );

            });

        });

    });

</script>
</footer>
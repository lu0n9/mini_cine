@extends('client.layouts.master')
@section('content')
    <div class="shell profile-container">
        <h1 class="page-title">Hồ sơ cá nhân</h1>

        <div class="card profile-card" id="profileCard">
            <!-- CHẾ ĐỘ XEM THÔNG TIN (Mặc định) -->
            <div class="profile-view-mode">
                <div class="avatar-profile-wrapper">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="avatar-profile avatar-img">
                    @else
                        <div class="avatar-profile">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
                    @endif
                </div>

                <div class="user-details">
                    <h2 class="user-name">{{ auth()->user()->name ?? 'Người dùng' }}</h2>
                    <p class="user-email">{{ auth()->user()->email }}</p>
                    <p class="user-joined">Tham gia từ {{ auth()->user()->created_at ? auth()->user()->created_at->format('d/m/Y') : 'Chưa xác định' }}</p>
                    <button type="button" class="btn btn-primary" id="btnEditProfile">Chỉnh sửa hồ sơ</button>
                </div>
            </div>

            <!-- CHẾ ĐỘ CHỈNH SỬA (Ẩn mặc định) -->
            <form class="profile-edit-mode" action="{{ route('profile.updateName') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="avatar-profile-wrapper">
                    <!-- Hiển thị / Xem trước Avatar -->
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="avatar-profile avatar-img" id="avatarPreview">
                    @else
                        <div class="avatar-profile" id="avatarPreviewText">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
                    @endif

                    <!-- Input chọn file ẩn -->
                    <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display: none;">

                    <!-- Nút bấm chọn ảnh -->
                    <button type="button" class="btn-avatar-edit" id="btnSelectAvatar" title="Đổi ảnh đại diện">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                            <circle cx="12" cy="13" r="4"></circle>
                        </svg>
                    </button>
                </div>

                <div class="user-details">
                    <div class="form-group">
                        <label for="userNameInput" class="form-label">Tên hiển thị</label>
                        <input 
                            type="text" 
                            id="userNameInput" 
                            name="name" 
                            class="form-input" 
                            value="{{ auth()->user()->name ?? '' }}"
                            required
                        >
                    </div>

                    <div class="edit-actions">
                        <button type="submit" class="btn btn-save">Lưu thay đổi</button>
                        <button type="button" class="btn btn-cancel-edit" id="btnCancelEdit">Hủy</button>
                    </div>
                </div>
            </form>
        </div>

        @php
            $activePremium = $premiumSubscription ?? $sharedPremiumSubscription;
            $remainingSeconds = $activePremium?->ends_at
                ? max(0, now()->diffInSeconds($activePremium->ends_at, false))
                : 0;
        @endphp
        <section class="card premium-profile-card" aria-labelledby="premiumProfileTitle">
            <div class="premium-profile-heading">
                <div>
                    <h2 class="card-title" id="premiumProfileTitle">Gói Premium</h2>
                    <p class="card-desc">
                        @if ($premiumSubscription)
                            Thông tin gói Premium tài khoản của bạn đã đăng ký.
                        @elseif ($sharedPremiumSubscription)
                            Bạn đang sử dụng Premium được chia sẻ.
                        @else
                            Tài khoản hiện chưa có gói Premium đang hoạt động.
                        @endif
                    </p>
                </div>
                @if (!$activePremium)
                    <a href="{{ route('premium.index') }}" class="btn btn-primary">Xem các gói</a>
                @endif
            </div>

            @if ($activePremium)
                <div class="premium-profile-details">
                    <div class="premium-profile-stat">
                        <span class="premium-profile-label">Gói đăng ký</span>
                        <strong>{{ $activePremium->plan?->name ?? 'Premium' }}</strong>
                    </div>
                    <div class="premium-profile-stat">
                        <span class="premium-profile-label">Thời gian còn lại</span>
                        <strong>
                            @if ($remainingSeconds >= 86400)
                                {{ (int) ceil($remainingSeconds / 86400) }} ngày
                            @elseif ($remainingSeconds >= 3600)
                                {{ (int) ceil($remainingSeconds / 3600) }} giờ
                            @else
                                Dưới 1 giờ
                            @endif
                        </strong>
                        <small>Hết hạn {{ $activePremium->ends_at?->format('d/m/Y H:i') ?? '—' }}</small>
                    </div>
                </div>
            @endif

            @if ($premiumSubscription && $premiumSubscription->shares->isNotEmpty())
                <div class="premium-shared-accounts">
                    <h3>Tài khoản đã chia sẻ</h3>
                    <ul>
                        @foreach ($premiumSubscription->shares as $share)
                            @if ($share->user)
                                <li>
                                    <span>{{ $share->user->name }}</span>
                                    <small>{{ $share->user->email }}</small>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @elseif ($sharedPremiumSubscription)
                <p class="premium-shared-by">
                    Gói này được chia sẻ bởi {{ $sharedPremiumSubscription->user?->name ?? 'một tài khoản Premium' }}
                    @if ($sharedPremiumSubscription->user?->email)
                        ({{ $sharedPremiumSubscription->user->email }})
                    @endif
                </p>
            @endif
        </section>

        <div class="card action-card">
            <div class="card-info">
                <h3 class="card-title">Mật khẩu</h3>
                <p class="card-desc">Thay đổi mật khẩu đăng nhập</p>
            </div>
            <button type="button" class="btn btn-secondary" id="toggleChangePasswordBtn">Đổi mật khẩu</button>
        </div>

        <!-- Form đổi mật khẩu - mặc định ẩn -->
        <div class="password-form-wrapper" id="passwordFormWrapper">

            <form
                class="password-form"
                id="changePasswordForm"
                action="{{ route('password.change') }}"
                method="POST"
            >
                @csrf

                {{-- Thông báo lỗi --}}
                @if ($errors->any())
                    <div class="auth-alert auth-alert--error">
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- Mật khẩu hiện tại --}}
                <div class="form-group">
                    <label for="currentPassword" class="form-label">
                        Mật khẩu hiện tại
                    </label>

                    <input
                        type="password"
                        id="currentPassword"
                        name="current_password"
                        class="form-input @error('current_password') is-invalid @enderror"
                        placeholder="Nhập mật khẩu hiện tại"
                        required
                    >

                    @error('current_password')
                        <span class="form-error">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Mật khẩu mới --}}
                <div class="form-group">
                    <label for="newPassword" class="form-label">
                        Mật khẩu mới
                    </label>

                    <input
                        type="password"
                        id="newPassword"
                        name="password"
                        class="form-input @error('password') is-invalid @enderror"
                        placeholder="Nhập mật khẩu mới"
                        required
                    >

                    @error('password')
                        <span class="form-error">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Xác nhận mật khẩu --}}
                <div class="form-group">
                    <label for="confirmPassword" class="form-label">
                        Xác nhận mật khẩu mới
                    </label>

                    <input
                        type="password"
                        id="confirmPassword"
                        name="password_confirmation"
                        class="form-input @error('password') is-invalid @enderror"
                        placeholder="Nhập lại mật khẩu mới"
                        required
                    >
                </div>

                <div class="form-actions">
                    <button
                        type="button"
                        class="btn btn-cancel"
                        id="cancelChangePasswordBtn"
                    >
                        Hủy
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Lưu mật khẩu
                    </button>
                </div>
            </form>

        </div>

        <div class="card action-card">
            <div class="card-info">
            <h3 class="card-title">Đăng xuất</h3>
            <p class="card-desc">Đăng xuất khỏi tài khoản trên thiết bị này</p>
            </div>
            <form action="{{ route('logout') }}" method="POST"> 
                @csrf 
                <button type="submit" class="btn btn-danger"> Đăng xuất </button> 
            </form>
        </div>
    </div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('toggleChangePasswordBtn');
    const cancelBtn = document.getElementById('cancelChangePasswordBtn');
    const passwordCard = toggleBtn.closest('.card'); // Tự động lấy card chứa nút bấm
    const formWrapper = document.getElementById('passwordFormWrapper');

    // Khi bấm Đổi mật khẩu: Ẩn card, Hiện form
    toggleBtn.addEventListener('click', () => {
        passwordCard.classList.add('hidden');
        formWrapper.classList.add('open');
    });

    // Khi bấm Hủy: Hiện lại card, Ẩn form
    cancelBtn.addEventListener('click', () => {
        formWrapper.classList.remove('open');
        passwordCard.classList.remove('hidden');
    });
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const profileCard = document.getElementById('profileCard');
    const btnEditProfile = document.getElementById('btnEditProfile');
    const btnCancelEdit = document.getElementById('btnCancelEdit');
    const editForm = profileCard.querySelector('.profile-edit-mode');

    // Element cho Avatar & Name
    const userNameDisplay = profileCard.querySelector('.user-name');
    const avatarInput = document.getElementById('avatarInput');
    const btnSelectAvatar = document.getElementById('btnSelectAvatar');
    const avatarPreview = document.getElementById('avatarPreview');
    const avatarPreviewText = document.getElementById('avatarPreviewText');

    let originalAvatarSrc = avatarPreview ? avatarPreview.src : null;

    // 1. Mở chế độ chỉnh sửa
    btnEditProfile.addEventListener('click', () => {
        profileCard.classList.add('is-editing');
    });

    // 2. Hủy chỉnh sửa & Khôi phục trạng thái ban đầu
    btnCancelEdit.addEventListener('click', () => {
        profileCard.classList.remove('is-editing');
        
        // Reset ô chọn file
        if (avatarInput) avatarInput.value = '';

        // Reset ảnh preview về trạng thái ban đầu
        if (avatarPreview && originalAvatarSrc) {
            avatarPreview.src = originalAvatarSrc;
        }
    });

    // 3. Mở ô chọn file khi bấm nút Camera
    if (btnSelectAvatar && avatarInput) {
        btnSelectAvatar.addEventListener('click', () => {
            avatarInput.click();
        });

        // Preview ảnh ngay khi người dùng chọn file mới
        avatarInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    if (avatarPreview) {
                        avatarPreview.src = event.target.result;
                    } else if (avatarPreviewText) {
                        // Nếu đang dùng chữ cái đại diện -> đổi thành thẻ <img>
                        const img = document.createElement('img');
                        img.src = event.target.result;
                        img.id = 'avatarPreview';
                        img.className = 'avatar-profile avatar-img';
                        avatarPreviewText.replaceWith(img);
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // 4. Gửi AJAX Cập nhật Tên + Avatar
    if (editForm) {
        editForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(editForm);
            const submitBtn = editForm.querySelector('.btn-save');
            const originalBtnText = submitBtn.innerText;

            submitBtn.innerText = 'Đang lưu...';
            submitBtn.disabled = true;

            try {
                const response = await fetch(editForm.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Cập nhật Tên trên giao diện
                    if (userNameDisplay) userNameDisplay.innerText = data.name;

                    // Cập nhật đường dẫn Avatar mới
                    if (data.avatar_url) {
                        originalAvatarSrc = data.avatar_url;
                        
                        // Cập nhật luôn avatar bên màn hình view
                        const viewAvatar = profileCard.querySelector('.profile-view-mode .avatar-profile-wrapper');
                        if (viewAvatar) {
                            viewAvatar.innerHTML = `<img src="${data.avatar_url}" alt="Avatar" class="avatar-profile avatar-img">`;
                        }
                    }

                    // Đóng form
                    profileCard.classList.remove('is-editing');
                } else {
                    alert(data.message || 'Có lỗi xảy ra, vui lòng thử lại.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Không thể kết nối đến máy chủ.');
            } finally {
                submitBtn.innerText = originalBtnText;
                submitBtn.disabled = false;
            }
        });
    }
});
</script>
@endsection

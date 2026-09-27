@extends('client.layouts.auth')

@section('title', 'Đặt lại mật khẩu')

@section('content')
<div class="auth-page">
  <div class="auth-page__bg" aria-hidden="true"></div>
  <div class="auth-page__overlay" aria-hidden="true"></div>

  <main class="auth-page__content">
    <div class="auth-card">
      <div class="auth-card__header">
        <h1 class="auth-card__title">Đặt lại mật khẩu</h1>
        <p class="auth-card__subtitle">Tạo mật khẩu mới cho tài khoản của bạn.</p>
      </div>

      @if ($errors->any())
        <div class="auth-alert auth-alert--error" role="alert">{{ $errors->first() }}</div>
      @endif

      <form action="{{ route('password.update') }}" method="POST" class="auth-form">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="auth-form__group">
          <label for="email" class="auth-form__label">Email</label>
          <input
            type="email"
            name="email"
            id="email"
            class="auth-form__input @error('email') is-invalid @enderror"
            value="{{ old('email', $email) }}"
            autocomplete="email"
            required
            autofocus
          >
          @error('email')
            <span class="auth-form__error">{{ $message }}</span>
          @enderror
        </div>

        <div class="auth-form__group">
          <label for="password" class="auth-form__label">Mật khẩu mới</label>
          <input
            type="password"
            name="password"
            id="password"
            class="auth-form__input @error('password') is-invalid @enderror"
            autocomplete="new-password"
            required
          >
          @error('password')
            <span class="auth-form__error">{{ $message }}</span>
          @enderror
        </div>

        <div class="auth-form__group">
          <label for="password_confirmation" class="auth-form__label">Xác nhận mật khẩu mới</label>
          <input
            type="password"
            name="password_confirmation"
            id="password_confirmation"
            class="auth-form__input"
            autocomplete="new-password"
            required
          >
        </div>

        <button type="submit" class="auth-form__button">Lưu mật khẩu mới</button>
      </form>

      <div class="auth-card__footer">
        <a href="{{ route('login') }}" class="auth-link auth-link--highlight">Quay lại đăng nhập</a>
      </div>
    </div>
  </main>
</div>
@endsection

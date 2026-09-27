@extends('client.layouts.auth')

@section('title', 'Quên mật khẩu')

@section('content')
<div class="auth-page">
  <div class="auth-page__bg" aria-hidden="true"></div>
  <div class="auth-page__overlay" aria-hidden="true"></div>

  <main class="auth-page__content">
    <div class="auth-card">
      <div class="auth-card__header">
        <h1 class="auth-card__title">Quên mật khẩu</h1>
        <p class="auth-card__subtitle">Nhập email đã đăng ký để nhận liên kết đặt lại mật khẩu.</p>
      </div>

      @if (session('status'))
        <div class="auth-alert auth-alert--success" role="status">{{ session('status') }}</div>
      @endif

      @if ($errors->any())
        <div class="auth-alert auth-alert--error" role="alert">{{ $errors->first() }}</div>
      @endif

      <form action="{{ route('password.email') }}" method="POST" class="auth-form">
        @csrf
        <div class="auth-form__group">
          <label for="email" class="auth-form__label">Email</label>
          <input
            type="email"
            name="email"
            id="email"
            class="auth-form__input @error('email') is-invalid @enderror"
            placeholder="nhapemail@example.com"
            value="{{ old('email') }}"
            autocomplete="email"
            required
            autofocus
          >
          @error('email')
            <span class="auth-form__error">{{ $message }}</span>
          @enderror
        </div>

        <button type="submit" class="auth-form__button">Gửi liên kết đặt lại</button>
      </form>

      <div class="auth-card__footer">
        <a href="{{ route('login') }}" class="auth-link auth-link--highlight">Quay lại đăng nhập</a>
      </div>
    </div>
  </main>
</div>
@endsection

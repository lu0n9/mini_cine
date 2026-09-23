@extends('client.layouts.auth')

@section('content')

<div class="auth-page">

  <!-- Layer Background hình nền poster phim + Overlay phủ tối -->

  <div class="auth-page__bg" aria-hidden="true"></div>

  <div class="auth-page__overlay" aria-hidden="true"></div>

  <!-- Khung chứa Form căn giữa màn hình -->

  <main class="auth-page__content">

    <div class="auth-card">

      <div class="auth-card__header">

        <h1 class="auth-card__title">Đăng nhập</h1>

        <p class="auth-card__subtitle">
          Chào mừng bạn quay trở lại với Mini Cine
        </p>

      </div>

      <form action="{{ route('register.submit') }}" method="POST" class="auth-form">

        @csrf

        {{-- Báo lỗi chung --}}

        @if (session('error'))

          <div class="auth-alert auth-alert--error">
            {{ session('error') }}
          </div>

        @endif


        {{-- Hiển thị tất cả lỗi validation nếu có --}}

        @if ($errors->any())

          <div class="auth-alert auth-alert--error">
            {{ $errors->first() }}
          </div>

        @endif

         <div class="auth-form__group">

          <label for="email" class="auth-form__label">
            Username
          </label>

          <input
            type="text"
            name="name"
            id="name"
            class="auth-form__input @error('name') is-invalid @enderror"
            placeholder="Enter username"
            value="{{ old('name') }}"
            required
            autofocus
          >

          @error('name')

            <span class="auth-form__error">
              {{ $message }}
            </span>

          @enderror

        </div>


        {{-- Input Email --}}

        <div class="auth-form__group">

          <label for="email" class="auth-form__label">
            Email
          </label>

          <input
            type="email"
            name="email"
            id="email"
            class="auth-form__input @error('email') is-invalid @enderror"
            placeholder="nhapemail@example.com"
            value="{{ old('email') }}"
            required
            autofocus
          >

          @error('email')

            <span class="auth-form__error">
              {{ $message }}
            </span>

          @enderror

        </div>


        {{-- Input Password --}}

        <div class="auth-form__group">

          <label for="password" class="auth-form__label">
            Password
          </label>

          <input
            type="password"
            name="password"
            id="password"
            class="auth-form__input @error('password') is-invalid @enderror"
            placeholder="••••••••"
            required
          >

          @error('password')

            <span class="auth-form__error">
              {{ $message }}
            </span>

          @enderror

        </div>

        <div class="auth-form__group">

          <label for="confirmPassword" class="auth-form__label">
            Confirm password
          </label>

          <input
            type="password"
            name="confirmPassword"
            id="confirmPassword"
            class="auth-form__input @error('confirmPassword') is-invalid @enderror"
            placeholder="••••••••"
            required
          >

          @error('confirmPassword')

            <span class="auth-form__error">
              {{ $message }}
            </span>

          @enderror

        </div>

        {{-- Ghi nhớ & Quên mật khẩu --}}

        <div class="auth-form__row">

          <label class="auth-checkbox">

            <input
              type="checkbox"
              name="remember"
              id="remember"
              {{ old('remember') ? 'checked' : '' }}
            >

            <span class="auth-checkbox__label">
              Ghi nhớ đăng nhập
            </span>

          </label>

          @if (Route::has('password.request'))

            <a
              href="{{ route('password.request') }}"
              class="auth-link"
            >
              Quên mật khẩu?
            </a>

          @else

            <a href="#" class="auth-link">
              Quên mật khẩu?
            </a>

          @endif

        </div>


        {{-- Nút Đăng nhập --}}

        <button type="submit" class="auth-form__button">
          Đăng nhập
        </button>

      </form>


      {{-- Chuyển sang Đăng ký --}}

      <div class="auth-card__footer">

        <p>
          Chưa có tài khoản?

          <a
            href="{{ route('register') }}"
            class="auth-link auth-link--highlight"
          >
            Đăng ký ngay
          </a>
        </p>

      </div>

    </div>

  </main>

</div>

@endsection
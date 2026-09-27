@extends('client.layouts.master')

@section('title', $notification->title)

@section('content')
<section class="shell user-notifications-page">
    <a href="{{ route('notifications.index') }}" class="user-notifications-back">← Tất cả thông báo</a>

    <article class="user-notification-detail">
        <p class="user-notifications-eyebrow">{{ $notification->type ?: 'Thông báo' }}</p>
        <h1>{{ $notification->title }}</h1>
        <time datetime="{{ $notification->created_at->toIso8601String() }}">
            {{ $notification->created_at->timezone(config('app.timezone'))->format('H:i, d/m/Y') }}
        </time>
        <div class="user-notification-detail__message">{{ $notification->message }}</div>

        @if ($notification->url)
            <a href="{{ $notification->url }}" class="user-notification-action">Xem chi tiết</a>
        @endif
    </article>
</section>
@endsection

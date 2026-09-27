@extends('client.layouts.master')

@section('title', 'Thông báo của tôi')

@section('content')
<section class="shell user-notifications-page">
    <header class="user-notifications-heading">
        <div>
            <p class="user-notifications-eyebrow">MINI CINE</p>
            <h1>Thông báo của tôi</h1>
            <p>Bạn có {{ number_format($unreadNotificationCount) }} thông báo chưa đọc.</p>
        </div>
    </header>

    @if ($notifications->isEmpty())
        <div class="user-notification-empty">Bạn chưa có thông báo nào.</div>
    @else
        <div class="user-notification-list">
            @foreach ($notifications as $notification)
                <a
                    href="{{ route('notifications.show', $notification->id) }}"
                    class="user-notification-card {{ $notification->read_at ? '' : 'is-unread' }}"
                >
                    <span class="user-notification-indicator" aria-hidden="true"></span>
                    <span class="user-notification-copy">
                        <strong>{{ $notification->title }}</strong>
                        <span>{{ $notification->message }}</span>
                        <time datetime="{{ $notification->created_at->toIso8601String() }}">
                            {{ $notification->created_at->timezone(config('app.timezone'))->format('H:i, d/m/Y') }}
                        </time>
                    </span>
                    <span class="user-notification-arrow" aria-hidden="true">›</span>
                </a>
            @endforeach
        </div>

        <div class="user-notifications-pagination">{{ $notifications->links() }}</div>
    @endif
</section>
@endsection

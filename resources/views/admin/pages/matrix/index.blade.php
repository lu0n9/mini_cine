@extends('admin.layouts.master')

@section('content')

@php

    $moduleLabels = [
        'dashboard' => 'Dashboard',
        'movies' => 'Movies',
        'episodes' => 'Episodes',
        'genres' => 'Genres',
        'countries' => 'Countries',
        'people' => 'People',
        'users' => 'Users',
        'comments' => 'Comments',
        'ratings' => 'Ratings',
        'reports' => 'Reports',
        'banners' => 'Banners',
        'notifications' => 'Notifications',
        'staff' => 'Staff',
        'roles' => 'Roles',
        'seasons' => 'Seasons',
        'servers' => 'Servers',
        'subtitles' => 'Subtitles',
        'statistics' => 'Statistics',
        'interface' => 'Interface',
        'email' => 'Email',
        'seo' => 'SEO',
        'sitemap' => 'Sitemap',
        'premium' => 'Premium',
        'system' => 'System',
        'settings' => 'Settings',
    ];

    $moduleIcons = [
        'dashboard' => '⌂',
        'movies' => '▣',
        'episodes' => '▶',
        'genres' => '◆',
        'countries' => '◎',
        'people' => '♟',
        'users' => '●',
        'comments' => '□',
        'ratings' => '★',
        'reports' => '⚑',
        'banners' => '▤',
        'notifications' => '◇',
        'staff' => '♟',
        'roles' => '◈',
        'seasons' => '▥',
        'servers' => '◉',
        'subtitles' => 'CC',
        'statistics' => '▦',
        'interface' => '▧',
        'email' => '✉',
        'seo' => '⌕',
        'sitemap' => '⌘',
        'premium' => '◆',
        'system' => '⚙',
        'settings' => '⚙',
    ];

    $actionLabels = [
        'view' => 'Xem',
        'create' => 'Thêm',
        'edit' => 'Sửa',
        'update' => 'Cập nhật',
        'delete' => 'Xóa',
        'approve' => 'Duyệt',
        'ban' => 'Khóa',
        'unban' => 'Mở khóa',
        'handle' => 'Xử lý',
        'manage' => 'Quản lý',
    ];

@endphp

<section class="permission-page">

    {{-- Header --}}
    <div class="permission-head">

        <div>
            <h3>Roles & Permissions</h3>

            <p>
                Tổng quan quyền hạn của từng role theo từng module.
            </p>
        </div>

        <div class="permission-legend">

            <span class="legend-item">
                <span class="legend-dot full"></span>
                Toàn quyền
            </span>

            <span class="legend-item">
                <span class="legend-dot partial"></span>
                Một phần
            </span>

            <span class="legend-item">
                <span class="legend-dot none"></span>
                Không có
            </span>

        </div>

    </div>


    {{-- Matrix --}}
    <div class="permission-panel">

        <div class="permission-table-wrapper">

            <table class="permission-table">

                <thead>

                    <tr>

                        <th>
                            Module
                        </th>

                        @foreach($roles as $role)

                            <th>
                                <span class="role-name">
                                    {{ $role->name }}
                                </span>
                            </th>

                        @endforeach

                    </tr>

                </thead>


                <tbody>

                    @foreach($modules as $module => $modulePermissions)

                        <tr>

                            {{-- Module --}}
                            <td>

                                <div class="module-name">

                                    <span class="module-icon">
                                        {{ $moduleIcons[$module] ?? '•' }}
                                    </span>

                                    <div class="module-info">

                                        <strong>
                                            {{ $moduleLabels[$module] ?? ucfirst($module) }}
                                        </strong>

                                        <span>
                                            {{ $modulePermissions->count() }} quyền
                                        </span>

                                    </div>

                                </div>

                            </td>


                            {{-- Roles --}}
                            @foreach($roles as $role)

                                @php
                                    $cell = $matrix[$role->id][$module];
                                @endphp

                                <td>

                                    <div class="permission-status">

                                        {{-- FULL --}}
                                        @if($cell['status'] === 'full')

                                            <div class="permission-summary full">

                                                <span class="status-icon">
                                                    ✓
                                                </span>

                                                <span>
                                                    Toàn quyền
                                                </span>

                                            </div>

                                        {{-- PARTIAL --}}
                                        @elseif($cell['status'] === 'partial')

                                            <div class="permission-summary partial">

                                                <span class="status-icon">
                                                    !
                                                </span>

                                                <span>
                                                    {{ $cell['granted'] }}/{{ $cell['total'] }} quyền
                                                </span>

                                            </div>

                                            <div class="permission-detail">

                                                <div class="permission-detail-title">
                                                    Quyền của role
                                                </div>

                                                <div class="permission-actions">

                                                    @foreach($cell['permissions'] as $permission)

                                                        @php
                                                            $action = explode('.', $permission->slug)[1] ?? '';
                                                            $label = $actionLabels[$action] ?? $action;
                                                            $hasPermission = $cell['granted_permissions']
                                                                ->contains('id', $permission->id);
                                                        @endphp

                                                        <span class="permission-action {{ !$hasPermission ? 'missing' : '' }}">

                                                            @if($hasPermission)
                                                                ✓
                                                            @else
                                                                −
                                                            @endif

                                                            {{ $label }}

                                                        </span>

                                                    @endforeach

                                                </div>

                                            </div>

                                        {{-- NONE --}}
                                        @else

                                            <div class="permission-summary none">

                                                <span class="status-icon">
                                                    −
                                                </span>

                                                <span>
                                                    Không có
                                                </span>

                                            </div>

                                        @endif

                                    </div>

                                </td>

                            @endforeach

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

</section>

@endsection
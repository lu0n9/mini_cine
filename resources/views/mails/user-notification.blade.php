<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Thông báo từ Mini Cine' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #0c0d12;
            color: #e2e8f0;
            margin: 0;
            padding: 30px 15px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        .email-wrapper {
            width: 100%;
            max-width: 580px;
            margin: 0 auto;
            background-color: #151822;
            border: 1px solid #232736;
            border-radius: 18px;
            padding: 40px 36px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.6);
            position: relative;
            overflow: hidden;
        }

        .email-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #e50914, #ff3b30, #ff9500);
        }

        .brand-logo {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 1px;
            color: #ffffff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .brand-logo-icon {
            width: 34px;
            height: 34px;
            background: #e50914;
            color: #ffffff;
            border-radius: 9px;
            display: inline-block;
            text-align: center;
            line-height: 34px;
            font-weight: 800;
            font-size: 19px;
        }

        .badge-type {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .badge-system { background: rgba(99, 102, 241, 0.18); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.35); }
        .badge-new_movie { background: rgba(229, 9, 20, 0.18); color: #f87171; border: 1px solid rgba(229, 9, 20, 0.35); }
        .badge-promotion { background: rgba(245, 158, 11, 0.18); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.35); }
        .badge-maintenance { background: rgba(249, 115, 22, 0.18); color: #fb923c; border: 1px solid rgba(249, 115, 22, 0.35); }
        .badge-account { background: rgba(16, 185, 129, 0.18); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.35); }

        .email-title {
            font-size: 22px;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.35;
            margin-bottom: 18px;
        }

        .greeting {
            font-size: 15px;
            color: #cbd5e1;
            margin-bottom: 20px;
        }

        .greeting strong {
            color: #ffffff;
        }

        .email-content {
            background: #1a1e2b;
            border: 1px solid #2d3345;
            border-radius: 12px;
            padding: 22px 24px;
            margin-bottom: 28px;
            color: #e2e8f0;
            font-size: 14.5px;
            line-height: 1.7;
            white-space: pre-line;
            word-break: break-word;
        }

        .cta-container {
            text-align: center;
            margin: 32px 0;
        }

        .cta-btn {
            display: inline-block;
            background: linear-gradient(135deg, #e50914, #b20710);
            color: #ffffff !important;
            font-size: 14.5px;
            font-weight: 700;
            text-decoration: none;
            padding: 13px 34px;
            border-radius: 10px;
            box-shadow: 0 6px 18px rgba(229, 9, 20, 0.4);
            letter-spacing: 0.5px;
        }

        .email-footer {
            border-top: 1px solid #232736;
            padding-top: 24px;
            margin-top: 28px;
            text-align: center;
            font-size: 12.5px;
            color: #64748b;
            line-height: 1.6;
        }

        .email-footer a {
            color: #94a3b8;
            text-decoration: none;
        }

        .email-footer p {
            margin-bottom: 6px;
        }

        @media only screen and (max-width: 600px) {
            body {
                padding: 15px 10px;
            }
            .email-wrapper {
                padding: 28px 20px;
            }
            .email-title {
                font-size: 19px;
            }
            .cta-btn {
                width: 100%;
                display: block;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <!-- HEADER -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 24px; border-bottom: 1px solid #232736; padding-bottom: 20px;">
            <tr>
                <td align="left" valign="middle">
                    <a href="{{ config('app.url', url('/')) }}" class="brand-logo" target="_blank">
                        <span class="brand-logo-icon">C</span>
                        <strong style="color:#ffffff; font-size:20px; letter-spacing:1px; vertical-align:middle;">MINI<span style="color:#e50914;">CINE</span></strong>
                    </a>
                </td>
                <td align="right" valign="middle">
                    @php
                        $badgeClass = match ($type ?? 'system') {
                            'new_movie' => 'badge-new_movie',
                            'promotion' => 'badge-promotion',
                            'maintenance' => 'badge-maintenance',
                            'account' => 'badge-account',
                            default => 'badge-system',
                        };
                        $badgeText = match ($type ?? 'system') {
                            'new_movie' => 'Phim mới',
                            'promotion' => 'Ưu đãi',
                            'maintenance' => 'Bảo trì',
                            'account' => 'Tài khoản',
                            default => 'Hệ thống',
                        };
                    @endphp
                    <span class="badge-type {{ $badgeClass }}">{{ $badgeText }}</span>
                </td>
            </tr>
        </table>

        <!-- TITLE -->
        <h1 class="email-title">{{ $title }}</h1>

        <!-- GREETING -->
        <p class="greeting">
            Xin chào <strong>{{ $user->name ?? 'Bạn' }}</strong>,
        </p>

        <!-- CONTENT -->
        <div class="email-content">{!! nl2br(e($contentMessage)) !!}</div>

        <!-- ACTION BUTTON -->
        @if(!empty($actionUrl))
            <div class="cta-container">
                <a href="{{ $actionUrl }}" class="cta-btn" target="_blank">
                    {{ $actionText ?: 'Xem chi tiết ngay' }} &rarr;
                </a>
            </div>
        @endif

        <!-- FOOTER -->
        <div class="email-footer">
            <p>Email này được gửi tự động từ hệ thống quản trị <strong>Mini Cine</strong>.</p>
            <p>Vui lòng không trả lời trực tiếp email này. Nếu bạn cần hỗ trợ, hãy truy cập <a href="{{ config('app.url', url('/')) }}" target="_blank">Trung tâm trợ giúp Mini Cine</a>.</p>
            <p style="margin-top: 14px; font-size: 11.5px; color: #475569;">
                &copy; {{ date('Y') }} Mini Cine Streaming Platform. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>


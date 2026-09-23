<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tài khoản bị khóa - Mini Cine</title>
    <!-- Google Fonts: Be Vietnam Pro hoặc Inter mang lại cảm giác hiện đại, tech -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #0b0b0b;
            color: #e5e5e5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            line-height: 1.5;
            padding: 20px;
        }

        .ban-container {
            width: 100%;
            max-width: 480px;
            background-color: #141414;
            border: 1px solid #262626;
            border-radius: 16px;
            padding: 40px 32px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
            position: relative;
            overflow: hidden;
        }

        /* Hiệu ứng ánh sáng rò rỉ mang âm hưởng điện ảnh ở góc trên */
        .ban-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 70%;
            height: 3px;
            background: linear-gradient(90deg, transparent, #e50914, transparent);
        }

        .brand-logo {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #ffffff;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .brand-logo span {
            color: #e50914;
        }

        .status-badge {
            display: inline-block;
            background-color: rgba(229, 9, 20, 0.1);
            color: #e50914;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 6px 12px;
            border-radius: 20px;
            margin-bottom: 16px;
            border: 1px solid rgba(229, 9, 20, 0.2);
        }

        h2 {
            font-size: 24px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 12px;
        }

        .greeting {
            color: #a3a3a3;
            font-size: 15px;
            margin-bottom: 24px;
        }

        .greeting strong {
            color: #ffffff;
        }

        .reason-box {
            background-color: #1f1f1f;
            border-left: 3px solid #e50914;
            padding: 16px;
            border-radius: 0 8px 8px 0;
            margin-bottom: 24px;
        }

        .reason-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #737373;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .reason-content {
            color: #f5f5f5;
            font-size: 14px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 24px;
            background-color: #1a1a1a;
            padding: 16px;
            border-radius: 8px;
            border: 1px solid #262626;
        }

        .info-item label {
            display: block;
            font-size: 12px;
            color: #737373;
            margin-bottom: 4px;
        }

        .info-item value, .info-item p {
            font-size: 14px;
            font-weight: 600;
            color: #ffffff;
        }

        .note {
            font-size: 13px;
            color: #a3a3a3;
            margin-bottom: 32px;
            border-top: 1px solid #262626;
            padding-top: 16px;
        }

        .footer {
            font-size: 14px;
            color: #737373;
        }

        .footer strong {
            color: #ffffff;
        }
    </style>
</head>
<body>

    <div class="ban-container">
        <!-- Logo thương hiệu kết hợp chất phim -->
        <div class="brand-logo">
            Mini<span>Cine</span>
        </div>

        <div class="status-badge">Tài khoản tạm ngưng hoạt động</div>

        <h2>Quyền truy cập đã bị hạn chế</h2>

        <p class="greeting">
            Xin chào, <strong>{{ $user->name }}</strong>. Tài khoản của bạn đã bị khóa bởi hệ thống quản trị.
        </p>

        <!-- Khung lý do nổi bật -->
        <div class="reason-box">
            <div class="reason-label">Lý do vi phạm</div>
            <div class="reason-content">
                {{ $user->ban_reason }}
            </div>
        </div>

        <!-- Thông tin thời gian dạng Grid hiện đại -->
        <div class="info-grid">
            <div class="info-item">
                <label>Thời gian khóa</label>
                <p>{{ $user->updated_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="info-item">
                <label>Thời gian mở khóa</label>
                <p>
                    @if($user->ban_expires_at)
                        {{ $user->ban_expires_at->format('d/m/Y H:i') }}
                    @else
                        Vĩnh viễn
                    @endif
                </p>
            </div>
        </div>

        <!-- Ghi chú chi tiết -->
        <div class="note">
            @if($user->ban_expires_at)
                Hệ thống sẽ tự động khôi phục quyền truy cập của bạn sau thời gian lịch trình bên trên.
            @else
                Đây là hình thức khóa tài khoản vĩnh viễn do vi phạm nghiêm trọng các tiêu chuẩn cộng đồng của chúng tôi.
            @endif
        </div>

        <div class="footer">
            Trân trọng,<br>
            <strong>Đội ngũ vận hành Mini Cine</strong>
        </div>
    </div>

</body>
</html>
<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Mail\UserBannedMail;
use App\Mail\UserNotificationMail;
use App\Models\Notification;
use App\Models\NotificationCampaign;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class NotificationController extends Controller
{
    /**
     * Hiển thị trang cấu hình Email & SMTP
     */
    public function email()
    {
        $smtpConfig = [
            'mail_mailer'       => config('mail.default', 'smtp'),
            'mail_host'         => config('mail.mailers.smtp.host', env('MAIL_HOST', 'smtp.gmail.com')),
            'mail_port'         => config('mail.mailers.smtp.port', env('MAIL_PORT', 587)),
            'mail_username'     => config('mail.mailers.smtp.username', env('MAIL_USERNAME', '')),
            'mail_password'     => config('mail.mailers.smtp.password', env('MAIL_PASSWORD', '')),
            'mail_encryption'   => config('mail.mailers.smtp.encryption', env('MAIL_ENCRYPTION', 'tls')),
            'mail_from_address' => config('mail.from.address', env('MAIL_FROM_ADDRESS', '')),
            'mail_from_name'    => config('mail.from.name', env('MAIL_FROM_NAME', 'Mini Cine')),
        ];

        return view('admin.pages.notifications.email', compact('smtpConfig'));
    }

    /**
     * Lưu cấu hình SMTP vào file .env
     */
    public function updateSmtp(Request $request)
    {
        $request->validate([
            'mail_mailer'       => 'required|string|in:smtp,sendmail,log',
            'mail_host'         => 'required_if:mail_mailer,smtp|nullable|string',
            'mail_port'         => 'required_if:mail_mailer,smtp|nullable|numeric',
            'mail_username'     => 'nullable|string',
            'mail_password'     => 'nullable|string',
            'mail_encryption'   => 'nullable|string|in:tls,ssl,none',
            'mail_from_address' => 'required|email',
            'mail_from_name'    => 'required|string|max:100',
        ], [
            'mail_host.required_if'      => 'Vui lòng nhập SMTP Host khi dùng driver SMTP.',
            'mail_port.required_if'      => 'Vui lòng nhập Port khi dùng driver SMTP.',
            'mail_from_address.required' => 'Vui lòng nhập địa chỉ email người gửi.',
            'mail_from_address.email'    => 'Địa chỉ email người gửi không hợp lệ.',
            'mail_from_name.required'   => 'Vui lòng nhập tên người gửi.',
        ]);

        $encryption = $request->mail_encryption === 'none' ? '' : ($request->mail_encryption ?: 'tls');

        $this->updateEnv([
            'MAIL_MAILER'       => $request->mail_mailer,
            'MAIL_HOST'         => $request->mail_host ?: 'smtp.gmail.com',
            'MAIL_PORT'         => $request->mail_port ?: '587',
            'MAIL_USERNAME'     => $request->mail_username ?: '',
            'MAIL_PASSWORD'     => $request->mail_password ?: '',
            'MAIL_ENCRYPTION'   => $encryption,
            'MAIL_FROM_ADDRESS' => $request->mail_from_address,
            'MAIL_FROM_NAME'    => $request->mail_from_name,
        ]);

        try {
            Artisan::call('config:clear');
        } catch (\Throwable $e) {}

        return back()->with('success', 'Đã lưu cấu hình Email SMTP thành công!');
    }

    /**
     * Gửi thử nghiệm email kiểm tra kết nối SMTP
     */
    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ], [
            'test_email.required' => 'Vui lòng nhập địa chỉ email nhận thử nghiệm.',
            'test_email.email'    => 'Địa chỉ email nhận không hợp lệ.',
        ]);

        // Nếu admin truyền cấu hình runtime từ form
        if ($request->filled('mail_host')) {
            $encryption = $request->mail_encryption === 'none' ? null : ($request->mail_encryption ?: 'tls');
            Config::set('mail.default', $request->mail_mailer ?: 'smtp');
            Config::set('mail.mailers.smtp.host', $request->mail_host);
            Config::set('mail.mailers.smtp.port', $request->mail_port);
            Config::set('mail.mailers.smtp.username', $request->mail_username);
            Config::set('mail.mailers.smtp.password', $request->mail_password);
            Config::set('mail.mailers.smtp.encryption', $encryption);
            Config::set('mail.from.address', $request->mail_from_address ?: config('mail.from.address'));
            Config::set('mail.from.name', $request->mail_from_name ?: config('mail.from.name'));
        }

        try {
            Mail::to($request->test_email)->sendNow(
                new UserNotificationMail(
                    title: 'Kiểm tra kết nối Email SMTP - Mini Cine',
                    contentMessage: "Xin chào,\n\nĐây là email kiểm tra kết nối từ hệ thống quản trị Mini Cine.\nNếu bạn nhận được email này, tính năng gửi email qua SMTP đang hoạt động chính xác và ổn định!\n\nThời gian kiểm tra: " . now()->format('H:i:s d/m/Y'),
                    actionText: 'Truy cập Mini Cine',
                    actionUrl: config('app.url', url('/')),
                    type: 'system'
                )
            );

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Gửi email thử nghiệm thành công tới ' . $request->test_email . '!',
                ]);
            }

            return back()->with('success', 'Gửi email thử nghiệm thành công tới ' . $request->test_email . '!');
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi kết nối SMTP: ' . $errorMessage,
                ], 422);
            }

            return back()->with('error', 'Lỗi kết nối SMTP: ' . $errorMessage);
        }
    }

    /**
     * Xem trước mẫu giao diện email (Live preview)
     */
    public function previewTemplate(Request $request)
    {
        $template = $request->query('template', 'user_notification');
        $type = $request->query('type', 'system');

        $sampleUser = User::first() ?? new User([
            'name' => 'Nguyễn Văn A',
            'email' => 'nguyenvana@example.com'
        ]);

        if ($template === 'user_banned') {
            return (new UserBannedMail($sampleUser))->render();
        }

        return (new UserNotificationMail(
            title: 'Tập 12 Crimson Vale Đã Lên Sóng Trọn Bộ 4K',
            contentMessage: "Xin chào bạn,\n\nTập mới nhất của bộ phim bạn đang theo dõi đã chính thức phát sóng trên hệ thống Mini Cine.\nTrải nghiệm hình ảnh chuẩn 4K HDR cùng âm thanh vòm Dolby Atmos đỉnh cao ngay hôm nay!\n\nChúc bạn có những giây phút thư giãn tuyệt vời.",
            actionText: 'Xem phim ngay',
            actionUrl: config('app.url', url('/')),
            type: $type,
            user: $sampleUser
        ))->render();
    }

    /**
     * Hiển thị trang quản lý & gửi thông báo
     */
    public function notification(Request $request)
    {
        $totalUsers = User::count();
        $users7Days = User::where('created_at', '>=', now()->subDays(7))->count();
        $users30Days = User::where('created_at', '>=', now()->subDays(30))->count();

        $suggestedUsers = User::select('id', 'name', 'email', 'created_at')
            ->latest()
            ->take(10)
            ->get();

        $campaigns = NotificationCampaign::with('admin')->latest()->paginate(10);

        return view('admin.pages.notifications.notifications', compact(
            'totalUsers',
            'users7Days',
            'users30Days',
            'suggestedUsers',
            'campaigns'
        ));
    }

    /**
     * AJAX: Xem trước số lượng người dùng theo tiêu chí lọc
     */
    public function previewUserCount(Request $request)
    {
        $targetType = $request->query('target_type', 'all');

        if ($targetType === 'all') {
            $count = User::count();
            return response()->json([
                'count' => $count,
                'description' => "Toàn bộ {$count} người dùng trong hệ thống",
            ]);
        }

        if ($targetType === 'single') {
            $userIds = $request->input('user_ids', []);
            if (!is_array($userIds)) {
                $userIds = explode(',', (string) $userIds);
            }
            $userIds = array_filter(array_map('trim', $userIds));
            $count = count($userIds);
            return response()->json([
                'count' => $count,
                'description' => "{$count} người dùng được chọn",
            ]);
        }

        if ($targetType === 'date_range') {
            [$startDate, $endDate] = $this->resolveDateRange(
                $request->query('date_preset'),
                $request->query('date_from'),
                $request->query('date_to')
            );

            $query = User::whereBetween('created_at', [$startDate, $endDate]);
            $count = $query->count();
            $sampleUsers = $query->select('id', 'name', 'email', 'created_at')
                ->latest()
                ->take(3)
                ->get()
                ->map(fn($u) => [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'created_at' => $u->created_at->format('d/m/Y'),
                ]);

            return response()->json([
                'count' => $count,
                'description' => "Đăng ký từ " . $startDate->format('d/m/Y') . " đến " . $endDate->format('d/m/Y'),
                'sample_users' => $sampleUsers,
            ]);
        }

        return response()->json(['count' => 0, 'description' => 'Không xác định']);
    }

    /**
     * AJAX: Tìm kiếm user theo từ khóa tên hoặc email
     */
    public function searchUsers(Request $request)
    {
        $keyword = trim((string) $request->query('q', ''));

        $query = User::select('id', 'name', 'email', 'avatar', 'created_at');

        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        $users = $query->latest()->take(20)->get()->map(function ($u) {
            return [
                'id'         => $u->id,
                'name'       => $u->name,
                'email'      => $u->email,
                'created_at' => $u->created_at ? $u->created_at->format('d/m/Y') : '—',
            ];
        });

        return response()->json($users);
    }

    /**
     * Xử lý gửi thông báo (Email SMTP + Web Notification)
     */
    public function sendNotification(Request $request)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'message'      => 'required|string',
            'type'         => 'required|string|in:system,new_movie,promotion,maintenance,account',
            'target_type'  => 'required|string|in:all,single,date_range',
            'channels'     => 'required|array|min:1',
            'channels.*'   => 'in:email,web',
            'action_text'  => 'nullable|string|max:100',
            'action_url'   => 'nullable|string|max:255',
            'user_ids'     => 'required_if:target_type,single|nullable|array',
            'date_preset'  => 'nullable|string',
            'date_from'    => 'required_if:target_type,date_range,custom|nullable|date',
            'date_to'      => 'required_if:target_type,date_range,custom|nullable|date',
        ], [
            'title.required'       => 'Vui lòng nhập tiêu đề thông báo.',
            'message.required'     => 'Vui lòng nhập nội dung thông báo.',
            'channels.required'    => 'Vui lòng chọn ít nhất 1 phương thức gửi (Email hoặc Web).',
            'user_ids.required_if' => 'Vui lòng chọn ít nhất một người dùng nhận thông báo.',
        ]);

        $channels = $request->input('channels', []);
        $channelEmail = in_array('email', $channels);
        $channelWeb = in_array('web', $channels);

        // 1. Xác định danh sách người dùng nhận thông báo
        $usersQuery = User::query();
        $targetDescription = '';

        if ($request->target_type === 'all') {
            $users = $usersQuery->get();
            $targetDescription = "Tất cả người dùng (" . $users->count() . ")";
        } elseif ($request->target_type === 'single') {
            $userIds = $request->input('user_ids', []);
            if (empty($userIds)) {
                return back()->with('error', 'Vui lòng chọn ít nhất một người dùng nhận thông báo.');
            }
            $users = $usersQuery->whereIn('id', $userIds)->get();
            $sampleNames = $users->pluck('name')->take(2)->implode(', ');
            $more = $users->count() > 2 ? ' và ' . ($users->count() - 2) . ' người khác' : '';
            $targetDescription = "{$users->count()} người dùng ({$sampleNames}{$more})";
        } else {
            // date_range
            [$startDate, $endDate] = $this->resolveDateRange(
                $request->date_preset,
                $request->date_from,
                $request->date_to
            );

            $users = $usersQuery->whereBetween('created_at', [$startDate, $endDate])->get();
            $targetDescription = "Đăng ký từ " . $startDate->format('d/m/Y') . " đến " . $endDate->format('d/m/Y') . " (" . $users->count() . " người)";
        }

        if ($users->isEmpty()) {
            return back()->with('error', 'Không tìm thấy người dùng nào thỏa mãn tiêu chuẩn đã chọn.');
        }

        $successCount = 0;
        $failCount = 0;
        $errors = [];

        // 2. Gửi thông báo đến từng user
        foreach ($users as $user) {
            $userSuccess = true;

            // Gửi qua Web Notification (Bảng notifications)
            if ($channelWeb) {
                try {
                    Notification::create([
                        'user_id' => $user->id,
                        'title'   => $request->title,
                        'message' => $request->message,
                        'type'    => $request->type,
                        'url'     => $request->action_url,
                        'read_at' => null,
                    ]);
                } catch (\Throwable $e) {
                    $userSuccess = false;
                    $errors[] = "Web [User {$user->id}]: " . $e->getMessage();
                }
            }

            // Gửi qua Email SMTP (Đưa vào hàng đợi Queue)
            if ($channelEmail && !empty($user->email)) {
                try {
                    Mail::to($user->email)->queue(
                        new UserNotificationMail(
                            title: $request->title,
                            contentMessage: $request->message,
                            actionText: $request->action_text,
                            actionUrl: $request->action_url,
                            type: $request->type,
                            user: $user
                        )
                    );
                } catch (\Throwable $e) {
                    $userSuccess = false;
                    $errors[] = "Email [{$user->email}]: " . $e->getMessage();
                }
            }

            if ($userSuccess) {
                $successCount++;
            } else {
                $failCount++;
            }
        }

        // 3. Lưu lịch sử chiến dịch thông báo
        $status = $failCount === 0 ? 'success' : ($successCount > 0 ? 'partial' : 'failed');

        NotificationCampaign::create([
            'admin_id'           => auth('admin')->id(),
            'title'              => $request->title,
            'message'            => $request->message,
            'type'               => $request->type,
            'target_type'        => $request->target_type,
            'target_description' => $targetDescription,
            'action_text'        => $request->action_text,
            'action_url'         => $request->action_url,
            'recipient_count'    => $successCount,
            'channel_email'      => $channelEmail,
            'channel_web'        => $channelWeb,
            'status'             => $status,
            'error_message'      => !empty($errors) ? implode("\n", array_slice($errors, 0, 5)) : null,
        ]);

        if ($status === 'success') {
            $channelNames = array_filter([
                $channelEmail ? 'Email' : null,
                $channelWeb ? 'thông báo trên Web' : null,
            ]);
            $channelSummary = implode(' và ', $channelNames);
            $queueNote = $channelEmail ? ' Email đã được đưa vào hàng đợi gửi.' : '';
            $msg = "Đã gửi {$channelSummary} thành công tới {$successCount} người dùng!{$queueNote}";
            return back()->with('success', $msg);
        } elseif ($status === 'partial') {
            return back()->with('warning', "Đã xử lý thông báo tới {$successCount} người dùng ({$failCount} lỗi đưa vào hàng đợi).");
        } else {
            return back()->with('error', "Không thể xử lý gửi thông báo. Chi tiết: " . implode('; ', array_slice($errors, 0, 3)));
        }
    }

    /**
     * Xóa 1 bản ghi lịch sử chiến dịch thông báo
     */
    public function deleteCampaign($id)
    {
        $campaign = NotificationCampaign::findOrFail($id);
        $campaign->delete();

        return back()->with('success', 'Đã xóa bản ghi lịch sử thông báo thành công.');
    }

    /**
     * Phân giải mốc thời gian đăng ký tài khoản
     */
    protected function resolveDateRange(?string $preset, ?string $from, ?string $to): array
    {
        if ($preset === '7_days') {
            return [now()->subDays(7)->startOfDay(), now()->endOfDay()];
        }
        if ($preset === '30_days') {
            return [now()->subDays(30)->startOfDay(), now()->endOfDay()];
        }
        if ($preset === '90_days') {
            return [now()->subDays(90)->startOfDay(), now()->endOfDay()];
        }
        if ($preset === 'year_to_date') {
            return [now()->startOfYear(), now()->endOfDay()];
        }

        $startDate = $from ? Carbon::parse($from)->startOfDay() : now()->subMonth()->startOfDay();
        $endDate = $to ? Carbon::parse($to)->endOfDay() : now()->endOfDay();

        return [$startDate, $endDate];
    }

    /**
     * Cập nhật an toàn các biến môi trường vào file .env
     */
    protected function updateEnv(array $data): void
    {
        $envPath = base_path('.env');
        if (!file_exists($envPath)) {
            return;
        }

        $content = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            $key = strtoupper(trim($key));
            $value = (string) $value;

            $needsQuotes = str_contains($value, ' ') || str_contains($value, '$') || str_contains($value, '#') || empty($value);
            $formattedValue = $needsQuotes ? '"' . addcslashes($value, '"\\') . '"' : $value;

            $pattern = "/^{$key}=.*/m";
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, "{$key}={$formattedValue}", $content);
            } else {
                $content .= "\n{$key}={$formattedValue}";
            }
        }

        file_put_contents($envPath, $content);
    }
}

<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Services\StorageDashboardService;
use App\Models\PaymentGatewayConfig;
use App\Models\AdminActivityLog;
use App\Services\SystemCronService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\Process\Process;
use ZipArchive;

class SystemController extends Controller
{
    public function activityLog(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:100',
            'action' => 'nullable|in:Tạo / thực hiện,Cập nhật,Đổi trạng thái,Xóa,Xóa cache,Khôi phục',
            'from' => 'nullable|date_format:Y-m-d',
            'to' => 'nullable|date_format:Y-m-d',
        ]);
        $logsQuery = AdminActivityLog::query()->latest('created_at');

        if ($request->filled('search')) {
            $term = trim((string) $request->input('search'));
            $logsQuery->where(function ($query) use ($term) {
                $query->where('admin_name', 'like', '%' . $term . '%')
                    ->orWhere('admin_email', 'like', '%' . $term . '%')
                    ->orWhere('action', 'like', '%' . $term . '%')
                    ->orWhere('subject', 'like', '%' . $term . '%')
                    ->orWhere('ip_address', 'like', '%' . $term . '%');
            });
        }
        if ($request->filled('action')) {
            $logsQuery->where('action', $request->string('action')->toString());
        }
        if ($request->filled('from')) {
            $logsQuery->whereDate('created_at', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $logsQuery->whereDate('created_at', '<=', $request->input('to'));
        }

        $logs = $logsQuery->paginate(25)->withQueryString();
        $todayCount = AdminActivityLog::whereDate('created_at', today())->count();
        $adminCount = AdminActivityLog::whereDate('created_at', today())
            ->whereNotNull('admin_id')->distinct()->count('admin_id');

        return view('admin.pages.system.activity_logs', compact('logs', 'todayCount', 'adminCount'));
    }

    /**
     * Danh sách API Keys & Services trong hệ thống.
     */
    public function api(Request $request)
    {
        ApiKey::syncProjectApis();

        // Build query with optional filters
        $query = ApiKey::query();

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Search by name (case‑insensitive)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // Pagination (15 items per page)
        $perPage = 15;
        $apiKeys = $query->orderBy('id', 'asc')->paginate($perPage)->appends($request->except('page'));

        // Distinct API types for filter dropdown / tabs
        $apiTypes = ApiKey::select('type')->distinct()->pluck('type');

        $apiTypeLabels = ApiKey::API_TYPES;
        $paymentConfig = PaymentGatewayConfig::where('provider', 'vnpay')->first();
        $vnpayConfigured = $paymentConfig
            ? (bool) ($paymentConfig->merchant_code && $paymentConfig->secret_key)
            : (bool) (config('services.vnpay.tmn_code') && config('services.vnpay.hash_secret'));
        $vnpayEnabled = $paymentConfig?->is_enabled ?? $vnpayConfigured;
        $vnpayUrl = $paymentConfig?->payment_url ?? config('services.vnpay.url');
        $vnpayMerchantCode = $paymentConfig?->merchant_code ?? config('services.vnpay.tmn_code');

        return view('admin.pages.system.api', compact(
            'apiKeys', 'apiTypes', 'apiTypeLabels', 'paymentConfig', 'vnpayConfigured', 'vnpayEnabled', 'vnpayUrl', 'vnpayMerchantCode'
        ));
    }

    public function savePaymentConfig(Request $request)
    {
        $paymentConfig = PaymentGatewayConfig::where('provider', 'vnpay')->first();
        $existingSecret = $paymentConfig?->secret_key ?: config('services.vnpay.hash_secret');
        $validator = Validator::make($request->all(), [
            'merchant_code' => ['required', 'string', 'max:100'],
            'secret_key' => [$existingSecret ? 'nullable' : 'required', 'string', 'max:500'],
            'payment_url' => ['required', Rule::in([
                'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html',
                'https://pay.vnpay.vn/vpcpay.html',
            ])],
            'is_enabled' => ['nullable', 'boolean'],
        ], [
            'merchant_code.required' => 'Vui lòng nhập mã website/merchant VNPay.',
            'secret_key.required' => 'Vui lòng nhập Secret Key VNPay lần đầu cấu hình.',
            'payment_url.in' => 'URL thanh toán VNPay không hợp lệ.',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->except('secret_key'));
        }
        $data = $validator->validated();

        $secret = trim((string) ($data['secret_key'] ?? ''));
        if ($secret === '') {
            $secret = $existingSecret;
        }
        $paymentConfig ??= new PaymentGatewayConfig(['provider' => 'vnpay']);
        $paymentConfig->fill([
            'merchant_code' => trim($data['merchant_code']),
            'secret_key' => $secret,
            'payment_url' => $data['payment_url'],
            'is_enabled' => $request->boolean('is_enabled'),
        ])->save();

        return redirect()->route('admin.system.api')
            ->with('success', 'Đã lưu cấu hình VNPay. Secret Key được mã hóa trong cơ sở dữ liệu.');
    }

    /**
     * Đồng bộ / làm mới danh sách các API thực tế từ dự án & .env.
     */
    public function syncProjectApis()
    {
        ApiKey::syncProjectApis();

        return redirect()->route('admin.system.api')->with('success', 'Đã đồng bộ các dịch vụ có cấu hình trong .env.');
    }

    /**
     * Lưu API Key / Dịch vụ mới.
     */
    public function storeApiKey(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_keys(ApiKey::API_TYPES)),
            'key' => 'nullable|string|max:500',
            'endpoint' => 'nullable|url|max:500',
            'rate_limit' => 'required|integer|min:1|max:100000',
            'expiry_preset' => 'nullable|string|in:never,30_days,90_days,1_year,custom',
            'custom_expires_at' => 'nullable|date|after:today',
            'status' => 'nullable|in:active,revoked',
        ], [
            'name.required' => 'Vui lòng nhập tên API nhận diện.',
            'type.required' => 'Vui lòng chọn loại API.',
            'type.in' => 'Loại API không hợp lệ.',
            'endpoint.url' => 'Endpoint phải là URL hợp lệ.',
            'rate_limit.required' => 'Vui lòng chọn hoặc nhập giới hạn tần suất (Rate limit).',
            'rate_limit.integer' => 'Rate limit phải là số nguyên dương.',
        ]);

        if (in_array($validated['type'], ['ai_moderation', 'video_storage'], true) && empty($validated['key'])) {
            return back()->withInput()->withErrors(['key' => 'Nhập API key thật để kết nối dịch vụ này.']);
        }
        if ($validated['type'] === 'video_storage' && empty($validated['endpoint'])) {
            return back()->withInput()->withErrors(['endpoint' => 'Nhập endpoint Supabase để kết nối lưu trữ video.']);
        }

        // Check for duplicate key if custom key is provided
        $customKey = !empty($validated['key']) ? trim($validated['key']) : null;
        if ($customKey && ApiKey::where('key', $customKey)->exists()) {
            return back()->withInput()->withErrors(['key' => 'Mã API Key đã tồn tại. Hãy dùng mã khác hoặc để trống để hệ thống tự tạo.']);
        }

        $expiresAt = null;
        $preset = $validated['expiry_preset'] ?? 'never';
        if ($preset === '30_days') {
            $expiresAt = now()->addDays(30);
        } elseif ($preset === '90_days') {
            $expiresAt = now()->addDays(90);
        } elseif ($preset === '1_year') {
            $expiresAt = now()->addYear();
        } elseif ($preset === 'custom' && !empty($validated['custom_expires_at'])) {
            $expiresAt = \Carbon\Carbon::parse($validated['custom_expires_at']);
        }
        if ($preset === 'custom' && empty($validated['custom_expires_at'])) {
            return back()->withInput()->withErrors(['custom_expires_at' => 'Chọn ngày hết hạn cho API.']);
        }

        $status = $validated['status'] ?? 'active';

        $result = ApiKey::createKey(
            name: $validated['name'],
            rateLimit: (int) $validated['rate_limit'],
            expiresAt: $expiresAt,
            customKey: $customKey,
            status: $status,
            type: $validated['type'],
            endpoint: $validated['endpoint'] ?? null
        );

        return redirect()->route('admin.system.api')
            ->with('success', 'Đã thêm API "' . $validated['name'] . '" thành công.')
            ->with('new_api_key', $result['plain_key'])
            ->with('new_api_name', $validated['name']);
    }

    /**
     * Giao diện chỉnh sửa API.
     */
    public function editApiKey($id)
    {
        $apiKey = ApiKey::findOrFail($id);

        return view('admin.pages.system.edit_api', compact('apiKey'));
    }

    /**
     * Cập nhật / Sửa thông tin API.
     */
    public function updateApiKey(Request $request, $id)
    {
        $apiKey = ApiKey::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_keys(ApiKey::API_TYPES)),
            'key' => 'nullable|string|max:500',
            'endpoint' => 'nullable|url|max:500',
            'rate_limit' => 'required|integer|min:1|max:100000',
            'status' => 'required|in:active,revoked',
            'expires_at' => 'nullable|date|after:now',
        ], [
            'name.required' => 'Vui lòng nhập tên API.',
            'type.required' => 'Vui lòng chọn loại API.',
            'endpoint.url' => 'Endpoint phải là URL hợp lệ.',
            'expires_at.after' => 'Ngày hết hạn phải nằm trong tương lai.',
            'rate_limit.required' => 'Vui lòng nhập giới hạn tần suất.',
            'rate_limit.integer' => 'Rate limit phải là số nguyên dương.',
            'status.required' => 'Vui lòng chọn trạng thái.',
        ]);

        $plainKey = isset($validated['key']) && trim($validated['key']) !== ''
            ? trim($validated['key'])
            : $apiKey->key;

        if (in_array($validated['type'], ['ai_moderation', 'video_storage'], true) && !$plainKey) {
            return back()->withInput()->withErrors(['key' => 'Nhập API key thật để kết nối dịch vụ này.']);
        }
        if ($validated['type'] === 'video_storage' && empty($validated['endpoint'])) {
            return back()->withInput()->withErrors(['endpoint' => 'Nhập endpoint Supabase để kết nối lưu trữ video.']);
        }

        // Check for duplicate key (exclude current record)
        if (ApiKey::where('key', $plainKey)->where('id', '!=', $id)->exists()) {
            return back()->withInput()->withErrors(['key' => 'Mã API Key đã thuộc về một API khác.']);
        }

        $prefix = substr($plainKey, 0, 7);
        $suffix = substr($plainKey, -4);

        $apiKey->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'key' => $plainKey,
            'key_prefix' => $prefix,
            'key_suffix' => $suffix,
            'endpoint' => $validated['endpoint'] ?? null,
            'rate_limit' => (int) $validated['rate_limit'],
            'status' => $validated['status'],
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        return redirect()->route('admin.system.api')
            ->with('success', 'Đã cập nhật thông tin API "' . $apiKey->name . '" thành công.');
    }

    /**
     * Thu hồi API Key.
     */
    public function revokeApiKey($id)
    {
        $apiKey = ApiKey::findOrFail($id);
        $apiKey->update(['status' => 'revoked']);

        return redirect()->route('admin.system.api')
            ->with('success', 'Đã thu hồi API "' . $apiKey->name . '". Khóa này sẽ không thể sử dụng để gửi request.');
    }

    /**
     * Kích hoạt lại API Key.
     */
    public function activateApiKey($id)
    {
        $apiKey = ApiKey::findOrFail($id);
        $apiKey->update(['status' => 'active']);

        return redirect()->route('admin.system.api')
            ->with('success', 'Đã kích hoạt lại API "' . $apiKey->name . '".');
    }

    /**
     * Xóa vĩnh viễn API Key.
     */
    public function deleteApiKey($id)
    {
        $apiKey = ApiKey::findOrFail($id);
        $name = $apiKey->name;
        $apiKey->delete();

        return redirect()->route('admin.system.api')
            ->with('success', 'Đã xóa API "' . $name . '" thành công.');
    }

    /**
     * Bulk toggle status of selected API keys (active ↔ revoked).
     */
    public function bulkToggle(Request $request)
    {
        $validated = $request->validate([
            'selected' => ['required', 'array', 'min:1'],
            'selected.*' => ['integer', 'distinct', 'exists:api_keys,id'],
        ]);
        $ids = $validated['selected'];
        if (empty($ids)) {
            return redirect()->route('admin.system.api')
                ->with('error', 'Không có API nào được chọn để thay đổi trạng thái.');
        }

        foreach ($ids as $id) {
            $apiKey = ApiKey::find($id);
            if ($apiKey) {
                $newStatus = $apiKey->status === 'active' ? 'revoked' : 'active';
                $apiKey->update(['status' => $newStatus]);
            }
        }

        return redirect()->route('admin.system.api')
            ->with('success', 'Đã cập nhật trạng thái cho các API đã chọn.');
    }

    public function cache()
    {
        $cacheStatus = $this->cacheStatus();

        return view('admin.pages.system.cache', compact('cacheStatus'));
    }

    public function clearCache(Request $request)
    {
        $data = $request->validate([
            'target' => ['required', Rule::in(['application', 'config', 'route', 'view'])],
        ]);

        $commands = [
            'application' => ['cache:clear', 'Application cache'],
            'config' => ['config:clear', 'Config cache'],
            'route' => ['route:clear', 'Route cache'],
            'view' => ['view:clear', 'View cache'],
        ];
        [$command, $label] = $commands[$data['target']];

        try {
            if (Artisan::call($command) !== 0) {
                throw new \RuntimeException("Artisan command {$command} returned a failure code.");
            }

            return redirect()->route('admin.system.cache')->with('success', "Đã xóa {$label}.");
        } catch (\Throwable $exception) {
            Log::error('Admin cache clear failed.', [
                'target' => $data['target'],
                'error' => $exception->getMessage(),
            ]);

            return redirect()->route('admin.system.cache')->with('error', "Không thể xóa {$label}. Kiểm tra log để biết chi tiết.");
        }
    }

    public function clearAllCache()
    {
        try {
            if (Artisan::call('optimize:clear') !== 0) {
                throw new \RuntimeException('Artisan command optimize:clear returned a failure code.');
            }

            return redirect()->route('admin.system.cache')->with('success', 'Đã xóa application, config, route, view và event cache.');
        } catch (\Throwable $exception) {
            Log::error('Admin full cache clear failed.', ['error' => $exception->getMessage()]);

            return redirect()->route('admin.system.cache')->with('error', 'Không thể xóa toàn bộ cache. Kiểm tra log để biết chi tiết.');
        }
    }

    private function cacheStatus(): array
    {
        $storeName = (string) config('cache.default');
        $store = config("cache.stores.{$storeName}", []);
        $driver = (string) ($store['driver'] ?? $storeName);
        $itemCount = null;
        $sizeBytes = null;

        try {
            if ($driver === 'database') {
                $table = $store['table'] ?? 'cache';
                if (Schema::hasTable($table)) {
                    $summary = DB::table($table)
                        ->where('expiration', '>', time())
                        ->selectRaw('COUNT(*) as item_count, COALESCE(SUM(LENGTH(value)), 0) as size_bytes')
                        ->first();
                    $itemCount = (int) $summary->item_count;
                    $sizeBytes = (int) $summary->size_bytes;
                } else {
                    $itemCount = 0;
                    $sizeBytes = 0;
                }
            } elseif ($driver === 'file') {
                [$itemCount, $sizeBytes] = $this->measureCacheDirectory($store['path'] ?? storage_path('framework/cache/data'));
            }
        } catch (\Throwable $exception) {
            Log::warning('Could not inspect application cache store.', [
                'store' => $storeName,
                'error' => $exception->getMessage(),
            ]);
        }

        $viewStats = $this->measureCacheDirectory(storage_path('framework/views'));

        return [
            'store' => $storeName,
            'driver' => $driver,
            'item_count' => $itemCount,
            'size' => $sizeBytes === null ? null : $this->formatCacheBytes($sizeBytes),
            'config_cached' => file_exists(app()->getCachedConfigPath()),
            'route_cached' => file_exists(app()->getCachedRoutesPath()),
            'view_count' => $viewStats[0],
            'view_size' => $this->formatCacheBytes($viewStats[1]),
        ];
    }

    private function measureCacheDirectory(string $path): array
    {
        if (!is_dir($path)) {
            return [0, 0];
        }

        $count = 0;
        $bytes = 0;
        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)
            );
            foreach ($iterator as $file) {
                if (!$file->isFile() || $file->isLink()) {
                    continue;
                }
                $count++;
                $bytes += max(0, (int) $file->getSize());
            }
        } catch (\Throwable $exception) {
            Log::warning('Could not measure a cache directory.', ['path' => $path, 'error' => $exception->getMessage()]);
        }

        return [$count, $bytes];
    }

    private function formatCacheBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        $units = ['KB', 'MB', 'GB', 'TB'];
        $unit = min((int) floor(log($bytes, 1024)) - 1, count($units) - 1);

        return number_format($bytes / (1024 ** ($unit + 1)), 2, ',', '.') . ' ' . $units[$unit];
    }
    public function backup()
    {
        $directory = storage_path('app/private/backups');
        $backups = collect(is_dir($directory) ? glob($directory . '/backup-*') : [])
            ->filter(fn ($path) => is_file($path))
            ->map(fn ($path) => [
                'name' => basename($path),
                'path' => $path,
                'type' => str_contains(basename($path), '-files-') ? 'Files' : 'Database',
                'size' => $this->formatBackupBytes((int) filesize($path)),
                'created_at' => \Illuminate\Support\Carbon::createFromTimestamp(filemtime($path)),
            ])
            ->sortByDesc(fn ($backup) => $backup['created_at']->timestamp)
            ->values();

        return view('admin.pages.system.backup', compact('backups'));
    }

    public function createBackup(Request $request)
    {
        @set_time_limit(0);
        $validated = $request->validate(['type' => 'required|in:database,files,full']);
        $directory = storage_path('app/private/backups');
        if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
            return back()->withErrors(['backup' => 'Không thể tạo thư mục lưu backup.']);
        }

        $created = [];
        try {
            if (in_array($validated['type'], ['database', 'full'], true)) {
                $created[] = $this->createDatabaseBackup($directory);
            }
            if (in_array($validated['type'], ['files', 'full'], true)) {
                $created[] = $this->createFilesBackup($directory);
            }
        } catch (\Throwable $exception) {
            foreach ($created as $path) {
                @unlink($path);
            }
            Log::error('Backup creation failed.', ['error' => $exception->getMessage()]);
            return back()->withErrors(['backup' => 'Tạo backup thất bại: ' . $exception->getMessage()]);
        }

        return redirect()->route('admin.system.backup')
            ->with('success', 'Đã tạo ' . count($created) . ' bản backup: ' . implode(', ', array_map('basename', $created)));
    }

    public function downloadBackup(string $filename)
    {
        $path = $this->backupPath($filename);
        abort_unless($path && is_file($path), 404);

        return response()->download($path, basename($path));
    }

    public function deleteBackup(string $filename)
    {
        $path = $this->backupPath($filename);
        abort_unless($path && is_file($path), 404);
        unlink($path);

        return redirect()->route('admin.system.backup')->with('success', 'Đã xóa bản backup ' . basename($path) . '.');
    }

    public function restoreBackup(Request $request, string $filename)
    {
        @set_time_limit(0);
        $request->validate(['confirmation' => 'required|in:RESTORE']);
        $path = $this->backupPath($filename);
        abort_unless($path && is_file($path), 404);
        $directory = dirname($path);

        try {
            if (str_ends_with($filename, '.sql')) {
                // Keep a fresh copy of the current database before replacing its contents.
                $this->createDatabaseBackup($directory);
                $this->restoreDatabaseBackup($path);
                $message = 'Đã khôi phục database. Hệ thống đã tạo backup database hiện tại trước khi khôi phục.';
            } elseif (str_ends_with($filename, '.zip')) {
                // Restore overlays the backed up upload files; make a rollback archive first.
                $this->createFilesBackup($directory);
                $this->restoreFilesBackup($path);
                $message = 'Đã chép file trong backup lên máy chủ (ghi đè file trùng tên). Hệ thống đã lưu trạng thái file hiện tại thành backup trước đó.';
            } else {
                abort(422, 'Định dạng backup không được hỗ trợ.');
            }
        } catch (\Throwable $exception) {
            Log::error('Backup restore failed.', ['backup' => $filename, 'error' => $exception->getMessage()]);
            return back()->withErrors(['backup' => 'Khôi phục thất bại: ' . $exception->getMessage()]);
        }

        return redirect()->route('admin.system.backup')->with('success', $message);
    }

    private function createDatabaseBackup(string $directory): string
    {
        $connection = config('database.connections.' . config('database.default'));
        $driver = $connection['driver'] ?? '';
        $name = 'backup-database-' . now()->format('Ymd-His') . '-' . bin2hex(random_bytes(3));
        $path = $directory . '/' . $name . ($driver === 'sqlite' ? '.sqlite' : '.sql');

        if ($driver === 'sqlite') {
            $source = $connection['database'] ?? null;
            if (!$source || !is_file($source) || !copy($source, $path)) {
                throw new \RuntimeException('Không thể sao chép file database SQLite.');
            }
            return $path;
        }

        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            throw new \RuntimeException('Hiện chỉ hỗ trợ backup database MySQL, MariaDB và SQLite.');
        }

        $binary = collect([
            '/Applications/XAMPP/xamppfiles/bin/mysqldump',
            '/usr/bin/mysqldump',
            '/opt/homebrew/bin/mysqldump',
            '/usr/local/bin/mysqldump',
        ])->first(fn ($candidate) => is_executable($candidate));
        if (!$binary) {
            throw new \RuntimeException('Không tìm thấy mysqldump.');
        }

        // Avoid --routines here: on upgraded MariaDB installations it queries mysql.proc,
        // which may have an outdated schema and cause the entire backup to fail.
        $arguments = [$binary, '--single-transaction', '--triggers', '--skip-lock-tables'];
        if (!empty($connection['unix_socket'])) {
            $arguments[] = '--socket=' . $connection['unix_socket'];
        } else {
            $arguments[] = '--host=' . ($connection['host'] ?? '127.0.0.1');
            $arguments[] = '--port=' . ($connection['port'] ?? '3306');
        }
        $arguments[] = '--user=' . ($connection['username'] ?? '');
        $arguments[] = $connection['database'];

        $output = fopen($path, 'wb');
        if (!$output) {
            throw new \RuntimeException('Không thể tạo file backup database.');
        }
        try {
            $process = new Process($arguments, base_path(), ['MYSQL_PWD' => (string) ($connection['password'] ?? '')]);
            $process->setTimeout(3600);
            $process->run(function ($type, $buffer) use ($output) {
                if ($type === Process::OUT) {
                    fwrite($output, $buffer);
                }
            });
            if (!$process->isSuccessful() || filesize($path) === 0) {
                throw new \RuntimeException(trim($process->getErrorOutput()) ?: 'mysqldump không tạo được dữ liệu.');
            }
        } catch (\Throwable $exception) {
            @unlink($path);
            throw $exception;
        } finally {
            fclose($output);
        }

        return $path;
    }

    private function createFilesBackup(string $directory): string
    {
        if (!class_exists(ZipArchive::class)) {
            throw new \RuntimeException('PHP extension zip chưa được bật.');
        }

        $path = $directory . '/backup-files-' . now()->format('Ymd-His') . '-' . bin2hex(random_bytes(3)) . '.zip';
        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::EXCL) !== true) {
            throw new \RuntimeException('Không thể tạo file ZIP backup.');
        }

        $sources = [
            'storage/app/public' => storage_path('app/public'),
            'public/uploads' => public_path('uploads'),
        ];
        try {
            foreach ($sources as $archivePrefix => $source) {
                if (!is_dir($source)) {
                    continue;
                }
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($source, \FilesystemIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );
                foreach ($iterator as $file) {
                    if (!$file->isFile() || $file->isLink()) {
                        continue;
                    }
                    $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($source) + 1));
                    if (!$zip->addFile($file->getPathname(), $archivePrefix . '/' . $relative)) {
                        throw new \RuntimeException('Không thể thêm file vào backup: ' . $relative);
                    }
                }
            }
        } catch (\Throwable $exception) {
            $zip->close();
            @unlink($path);
            throw $exception;
        }
        if (!$zip->close()) {
            @unlink($path);
            throw new \RuntimeException('Không thể hoàn tất file ZIP backup.');
        }

        return $path;
    }

    private function restoreDatabaseBackup(string $path): void
    {
        $connection = config('database.connections.' . config('database.default'));
        $driver = $connection['driver'] ?? '';
        if ($driver === 'sqlite' && str_ends_with($path, '.sqlite')) {
            DB::disconnect();
            if (!copy($path, $connection['database'])) {
                throw new \RuntimeException('Không thể ghi lại database SQLite.');
            }
            DB::reconnect();
            return;
        }
        if (!in_array($driver, ['mysql', 'mariadb'], true) || !str_ends_with($path, '.sql')) {
            throw new \RuntimeException('Backup không tương thích với loại database hiện tại.');
        }

        $binary = collect([
            '/Applications/XAMPP/xamppfiles/bin/mysql',
            '/usr/bin/mysql',
            '/opt/homebrew/bin/mysql',
            '/usr/local/bin/mysql',
        ])->first(fn ($candidate) => is_executable($candidate));
        if (!$binary) {
            throw new \RuntimeException('Không tìm thấy chương trình mysql để khôi phục database.');
        }
        $arguments = [$binary];
        if (!empty($connection['unix_socket'])) {
            $arguments[] = '--socket=' . $connection['unix_socket'];
        } else {
            $arguments[] = '--host=' . ($connection['host'] ?? '127.0.0.1');
            $arguments[] = '--port=' . ($connection['port'] ?? '3306');
        }
        $arguments[] = '--user=' . ($connection['username'] ?? '');
        $arguments[] = $connection['database'];
        $input = fopen($path, 'rb');
        $process = new Process($arguments, base_path(), ['MYSQL_PWD' => (string) ($connection['password'] ?? '')]);
        $process->setInput($input);
        $process->setTimeout(3600);
        try {
            $process->mustRun();
        } finally {
            fclose($input);
        }
    }

    private function restoreFilesBackup(string $path): void
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new \RuntimeException('File ZIP backup không đọc được.');
        }
        try {
            for ($index = 0; $index < $zip->numFiles; $index++) {
                $entry = str_replace('\\', '/', $zip->getNameIndex($index));
                if (str_contains($entry, '../') || str_starts_with($entry, '/') ||
                    !(str_starts_with($entry, 'storage/app/public/') || str_starts_with($entry, 'public/uploads/'))) {
                    throw new \RuntimeException('Backup chứa đường dẫn file không hợp lệ.');
                }
                if (str_ends_with($entry, '/')) {
                    continue;
                }
                $target = base_path($entry);
                $directory = dirname($target);
                if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
                    throw new \RuntimeException('Không thể tạo thư mục khôi phục file.');
                }
                $input = $zip->getStream($zip->getNameIndex($index));
                $output = fopen($target, 'wb');
                if (!$input || !$output) {
                    throw new \RuntimeException('Không thể ghi file khôi phục: ' . $entry);
                }
                stream_copy_to_stream($input, $output);
                fclose($input);
                fclose($output);
            }
        } finally {
            $zip->close();
        }
    }

    private function backupPath(string $filename): ?string
    {
        if (!preg_match('/^backup-(?:database|files)-[A-Za-z0-9-]+\.(?:sql|sqlite|zip)$/', $filename)) {
            return null;
        }
        return storage_path('app/private/backups/' . $filename);
    }

    private function formatBackupBytes(int $bytes): string
    {
        if ($bytes < 1024) return $bytes . ' B';
        $units = ['KB', 'MB', 'GB', 'TB'];
        $unit = min((int) floor(log($bytes, 1024)) - 1, count($units) - 1);
        return number_format($bytes / (1024 ** ($unit + 1)), 2, ',', '.') . ' ' . $units[$unit];
    }
    public function cron()
    {
        $definitions = SystemCronService::jobs();
        $runs = \App\Models\SystemCronRun::whereIn('job_key', array_keys($definitions))
            ->get()->keyBy('job_key');

        return view('admin.pages.system.cron', compact('definitions', 'runs'));
    }

    public function runCronJob(Request $request, string $job, SystemCronService $cron)
    {
        abort_unless(array_key_exists($job, SystemCronService::jobs()), 404);

        try {
            $run = $cron->run($job);
            return redirect()->route('admin.system.cron')->with(
                $run->status === 'success' ? 'success' : 'error',
                $run->message
            );
        } catch (\Throwable $exception) {
            Log::error('Manual scheduled task failed.', ['job' => $job, 'error' => $exception->getMessage()]);
            return redirect()->route('admin.system.cron')->with('error', 'Tác vụ thất bại: ' . $exception->getMessage());
        }
    }
    public function storage(StorageDashboardService $storageDashboard)
    {
        $storage = $storageDashboard->overview();

        return view('admin.pages.system.storage', compact('storage'));
    }
}

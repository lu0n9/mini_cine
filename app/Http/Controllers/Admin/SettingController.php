<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Hiển thị trang cài đặt hệ thống
     */
    public function index()
    {
        $settings = SystemSetting::getSettings();

        return view('admin.pages.settings.index', compact('settings'));
    }

    /**
     * Lưu cấu hình cài đặt hệ thống
     */
    public function update(Request $request)
    {
        $request->validate([
            'site_name'       => 'required|string|max:255',
            'contact_email'   => 'nullable|email|max:255',
            'contact_hotline' => 'nullable|string|max:50',
            'timezone'        => 'required|timezone',
            'player_autoplay' => 'nullable|boolean',
            'player_auto_next' => 'nullable|boolean',
            'player_pip' => 'nullable|boolean',
            'allow_registration' => 'nullable|boolean',
            'maintenance_mode' => 'nullable|boolean',
        ], [
            'site_name.required' => 'Tên website không được để trống.',
            'contact_email.email'=> 'Email liên hệ không đúng định dạng email.',
            'timezone.timezone' => 'Múi giờ không hợp lệ.',
        ]);

        $settings = SystemSetting::getSettings();

        $settings->update([
            'site_name'                => $request->site_name,
            'contact_email'            => $request->contact_email,
            'contact_hotline'          => $request->contact_hotline,
            'timezone'                 => $request->timezone,
            'maintenance_mode'         => $request->boolean('maintenance_mode'),
            'player_autoplay'          => $request->boolean('player_autoplay'),
            'player_auto_next'         => $request->boolean('player_auto_next'),
            'player_pip'               => $request->boolean('player_pip'),
            'allow_registration'       => $request->boolean('allow_registration'),
        ]);

        SystemSetting::clearCache();

        return back()->with('success', 'Đã lưu cấu hình cài đặt hệ thống thành công!');
    }
}

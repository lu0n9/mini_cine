<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UserAuthController extends Controller
{
    /**
     * Hiển thị trang đăng nhập
     */
    public function showLogin()
    {
        return view('client.pages.auth.login');
    }

    public function showForgotPassword()
    {
        return view('client.pages.auth.forgot-password');
    }

    public function sendPasswordResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
        ]);

        Password::broker('users')->sendResetLink(
            $request->only('email')
        );

        // Luôn hiển thị cùng một thông báo để không tiết lộ email có đăng ký hay không.
        return back()->with(
            'status',
            'Nếu email này đã đăng ký, hướng dẫn đặt lại mật khẩu sẽ được gửi đến hộp thư của bạn.'
        );
    }

    public function showResetPassword(string $token)
    {
        return view('client.pages.auth.reset-password', [
            'token' => $token,
            'email' => request()->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        $status = Password::broker('users')->reset(
            $validated,
            function (User $user) use ($validated) {
                $user->forceFill([
                    'password' => $validated['password'],
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with(
                'success',
                'Đặt lại mật khẩu thành công. Bạn có thể đăng nhập bằng mật khẩu mới.'
            );
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'Liên kết đặt lại mật khẩu không hợp lệ, đã hết hạn hoặc thông tin tài khoản không chính xác.',
            ]);
    }

    /**
     * Xử lý đăng nhập
     */
    public function login(Request $request)
    {
        
        // Validate dữ liệu
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user && !$user->is_active) {

            if ($user->banHasExpired()) {
                $user->activateFromBan();
            } else {
                return back()
                    ->withErrors([
                        'email' => 'Tài khoản của bạn đã bị khóa.',
                    ])
                    ->withInput(
                        $request->only('email')
                    );
            }
        }

        // Kiểm tra email + password
        if (!Auth::guard('web')->attempt(
            [
                'email' => $credentials['email'],
                'password' => $credentials['password'],
            ],
            $request->boolean('remember')
        )) {

            // Trả lỗi về field email
            throw ValidationException::withMessages([
                'email' => 'Email hoặc mật khẩu không chính xác.',
            ]);
        }

        // Chống session fixation
        $request->session()->regenerate();

        $user = Auth::guard('web')->user();

        $user->update([
            'last_login_at' => now(),
        ]);

        // Đăng nhập thành công
        return redirect()
        ->intended(route('home'))
        ->with('success', 'Đăng nhập thành công!');
    }


    public function showRegister()
    {
        if (!SystemSetting::get('allow_registration', true)) {
            return redirect()->route('login')->with('error', 'Đăng ký tài khoản hiện đang tạm đóng.');
        }

        return view('client.pages.auth.register');
    }

    /**
     * Xử lý đăng ký tài khoản
     */

    public function register(Request $request)
    {
        if (!SystemSetting::get('allow_registration', true)) {
            return redirect()->route('login')->with('error', 'Đăng ký tài khoản hiện đang tạm đóng.');
        }

        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'unique:users,email'],
                'password' => ['required', 'string', 'min:6'],
                'confirmPassword' => ['required', 'same:password'],
            ],
            [
                'name.required' => 'Vui lòng nhập tên.',
                'name.string' => 'Tên phải là chuỗi ký tự.',
                'name.max' => 'Tên không được vượt quá 255 ký tự.',
                'email.required' => 'Vui lòng nhập email.',
                'email.email' => 'Email không hợp lệ.',
                'email.unique' => 'Email này đã được sử dụng.',
                'password.required' => 'Vui lòng nhập mật khẩu.',
                'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
                'confirmPassword.required' => 'Vui lòng xác nhận mật khẩu.',
                'confirmPassword.same' => 'Mật khẩu xác nhận không khớp.',
            ]
        );

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Đăng nhập ngay sau khi đăng ký
        Auth::login($user);

        // Tạo session mới
        $request->session()->regenerate();

        return redirect()
            ->intended(route('home'))
            ->with('success', 'Đăng ký tài khoản thành công!');
    }

    public function showProfile()
    {
        $user = Auth::user();

        $premiumSubscription = $user->premiumSubscriptions()
            ->with(['plan', 'shares.user'])
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->latest('ends_at')
            ->first();

        $sharedPremiumSubscription = null;

        if (!$premiumSubscription) {
            $sharedPremiumSubscription = $user->premiumShares()
                ->with('subscription.plan', 'subscription.user')
                ->whereHas('subscription', function ($query) {
                    $query->where('status', 'active')->where('ends_at', '>', now());
                })
                ->latest()
                ->first()?->subscription;
        }

        return view('client.pages.auth.profile', compact(
            'premiumSubscription',
            'sharedPremiumSubscription'
        ));
    }


    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => [
                'required',
            ],

            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
            ],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',

            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        $user = $request->user();

        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check(
            $validated['current_password'],
            $user->password
        )) {
            return back()
                ->withErrors([
                    'current_password' => 'Mật khẩu hiện tại không chính xác.',
                ])
                ->with('open_password_form', true);
        }

        // Không cho đổi thành mật khẩu cũ
        if (Hash::check(
            $validated['password'],
            $user->password
        )) {
            return back()
                ->withErrors([
                    'password' => 'Mật khẩu mới phải khác mật khẩu hiện tại.',
                ])
                ->with('open_password_form', true);
        }

        // Cập nhật mật khẩu
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with(
            'success',
            'Đổi mật khẩu thành công!'
        );
    }


    /**
     * Đăng xuất
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        // $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Đăng xuất thành công!');
    }

    public function updateName(Request $request)
    {
        // 1. Validate dữ liệu đầu vào
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Tối đa 2MB
        ], [
            'name.required' => 'Vui lòng nhập tên hiển thị.',
            'name.max'      => 'Tên hiển thị không được quá 255 ký tự.',
            'avatar.image'  => 'Tệp tải lên phải là hình ảnh.',
            'avatar.mimes'  => 'Ảnh đại diện chỉ chấp nhận định dạng: jpeg, png, jpg, gif, webp.',
            'avatar.max'    => 'Dung lượng ảnh tối đa là 2MB.',
        ]);

        $user = $request->user();
        $user->name = $validated['name'];

        // 2. Xử lý Upload Avatar (nếu có chọn file)
        if ($request->hasFile('avatar')) {
            // Xóa avatar cũ trong ổ cứng nếu đã tồn tại
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Lưu ảnh mới vào thư mục storage/app/public/avatars
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        // 3. Lưu vào Database
        $user->save();

        // 4. Trả về Response JSON
        return response()->json([
            'success'    => true,
            'message'    => 'Cập nhật hồ sơ thành công!',
            'name'       => $user->name,
            'avatar_url' => $user->avatar ? asset('storage/' . $user->avatar) : null,
        ]);
    }
}

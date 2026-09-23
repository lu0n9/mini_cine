<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Danh sách Admin
     */
    public function index(Request $request)
    {
        $query = Admin::query()
            ->with([
                'roles' => function ($query) {
                    $query->where('is_active', true);
                }
            ]);

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $admins = $query
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.pages.admins.index', compact('admins'));
    }


    /**
     * Form tạo Admin
     */
    public function create()
    {
        $roles = AdminRole::query()
            ->where('is_active', true)
            ->orderBy('is_super_admin', 'desc')
            ->orderBy('name')
            ->get();

        return view('admin.pages.admins.create', compact('roles'));
    }


    /**
     * Lưu Admin mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:admins,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'role_id' => [
                'required',
                'integer',
                'exists:admin_roles,id',
            ],
        ], [
            'name.required' => 'Vui lòng nhập tên Admin.',

            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email này đã tồn tại trong hệ thống.',

            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',

            'role_id.required' => 'Vui lòng chọn Role.',
            'role_id.exists' => 'Role không tồn tại.',
        ]);

        /*
         * Không cho phép chọn role Super Admin
         * khi tài khoản hiện tại không phải Super Admin.
         */
        $role = AdminRole::findOrFail($validated['role_id']);

        $currentAdmin = auth('admin')->user();

        if (
            $role->is_super_admin &&
            !$currentAdmin->roles()
                ->where('is_super_admin', true)
                ->where('is_active', true)
                ->exists()
        ) {
            return back()
                ->withErrors([
                    'role_id' => 'Bạn không có quyền tạo tài khoản Super Admin.'
                ])
                ->withInput();
        }

        $admin = Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Gán Role
        $admin->roles()->sync([
            $role->id
        ]);

        return redirect()
            ->route('admin.admins')
            ->with('success', 'Đã tạo tài khoản Admin thành công.');
    }


    /**
     * Form chỉnh sửa Admin
     */
    public function edit(Admin $admin)
    {
        $roles = AdminRole::query()
            ->where('is_active', true)
            ->orderBy('is_super_admin', 'desc')
            ->orderBy('name')
            ->get();

        $currentRoleId = $admin->roles()
            ->value('admin_roles.id');

        return view(
            'admin.pages.admins.edit',
            compact(
                'admin',
                'roles',
                'currentRoleId'
            )
        );
    }


    /**
     * Cập nhật Admin
     */
    public function update(Request $request, Admin $admin)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('admins', 'email')
                    ->ignore($admin->id),
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],

            'role_id' => [
                'required',
                'integer',
                'exists:admin_roles,id',
            ],
        ], [
            'name.required' => 'Vui lòng nhập tên Admin.',

            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email này đã được sử dụng.',

            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',

            'role_id.required' => 'Vui lòng chọn Role.',
            'role_id.exists' => 'Role không tồn tại.',
        ]);

        $currentAdmin = auth('admin')->user();

        $newRole = AdminRole::findOrFail($validated['role_id']);

        /*
         * Không cho Admin thường:
         * - gán Super Admin
         */
        if (
            $newRole->is_super_admin &&
            !$currentAdmin->roles()
                ->where('is_super_admin', true)
                ->where('is_active', true)
                ->exists()
        ) {
            return back()
                ->withErrors([
                    'role_id' => 'Bạn không có quyền gán Role Super Admin.'
                ])
                ->withInput();
        }

        /*
         * Không cho Admin thường sửa Role của chính mình
         * thành Super Admin.
         */
        if (
            $admin->id === $currentAdmin->id &&
            $newRole->is_super_admin &&
            !$currentAdmin->roles()
                ->where('is_super_admin', true)
                ->exists()
        ) {
            return back()
                ->withErrors([
                    'role_id' => 'Bạn không thể tự cấp quyền Super Admin cho chính mình.'
                ])
                ->withInput();
        }

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        // Chỉ đổi mật khẩu khi người quản trị nhập mật khẩu mới
        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $admin->update($data);

        // Cập nhật Role
        $admin->roles()->sync([
            $newRole->id
        ]);

        return redirect()
            ->route('admin.admins')
            ->with('success', 'Đã cập nhật tài khoản Admin.');
    }


    /**
     * Xóa Admin
     */
    public function destroy(Admin $admin)
    {
        $currentAdmin = auth('admin')->user();

        // Không được tự xóa chính mình
        if ($admin->id === $currentAdmin->id) {
            return back()->with(
                'error',
                'Bạn không thể tự xóa tài khoản đang đăng nhập.'
            );
        }

        // Không cho xóa Super Admin
        $isSuperAdmin = $admin->roles()
            ->where('is_super_admin', true)
            ->where('is_active', true)
            ->exists();

        if ($isSuperAdmin) {
            return back()->with(
                'error',
                'Không thể xóa tài khoản Super Admin.'
            );
        }

        $admin->roles()->detach();

        $admin->delete();

        return back()->with(
            'success',
            'Đã xóa tài khoản Admin.'
        );
    }
}
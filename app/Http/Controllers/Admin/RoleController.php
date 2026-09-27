<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AdminRole;
use App\Models\AdminPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Danh sách Role
     */
    public function index()
    {
        $roles = AdminRole::query()
            ->withCount([
                'permissions',
                'admins',
            ])
            ->orderByDesc('is_super_admin')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.pages.roles.index', compact('roles'));
    }


    /**
     * Form thêm Role
     */
    public function create()
    {
        $permissions = AdminPermission::query()
            ->orderBy('module')
            ->orderBy('name')
            ->get()
            ->groupBy('module');

        return view('admin.pages.roles.create', compact('permissions'));
    }


    /**
     * Lưu Role
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:255',
                'unique:admin_roles,slug',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'permissions' => [
                'nullable',
                'array',
            ],

            'permissions.*' => [
                'integer',
                'exists:admin_permissions,id',
            ],
        ], [
            'name.required' => 'Vui lòng nhập tên Role.',
            'name.max' => 'Tên Role không được vượt quá 255 ký tự.',
            'slug.unique' => 'Slug này đã tồn tại.',
            'permissions.*.exists' => 'Permission không hợp lệ.',
        ]);

        $slug = $validated['slug']
            ?? Str::slug($validated['name']);

        // Kiểm tra slug sau khi tự động tạo
        if (AdminRole::where('slug', $slug)->exists()) {
            return back()
                ->withInput()
                ->withErrors([
                    'slug' => 'Slug đã tồn tại. Vui lòng chọn tên khác.',
                ]);
        }

        $role = AdminRole::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'is_super_admin' => false,
            'is_active' => true,
        ]);

        $role->permissions()->sync(
            $validated['permissions'] ?? []
        );

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Đã tạo Role thành công.');
    }


    /**
     * Form sửa Role
     */
    public function edit(AdminRole $role)
    {
        $permissions = AdminPermission::query()
            ->orderBy('module')
            ->orderBy('name')
            ->get()
            ->groupBy('module');

        $rolePermissionIds = $role->permissions()
            ->pluck('admin_permissions.id')
            ->toArray();

        return view(
            'admin.pages.roles.edit',
            compact(
                'role',
                'permissions',
                'rolePermissionIds'
            )
        );
    }


    /**
     * Cập nhật Role
     */
    public function update(Request $request, AdminRole $role)
    {
        // Không cho sửa trạng thái Super Admin thành Role thường
        if ($role->is_super_admin) {
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'description' => [
                    'nullable',
                    'string',
                ],
            ], [
                'name.required' => 'Vui lòng nhập tên Role.',
            ]);

            $role->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            return redirect()
                ->route('admin.roles.index')
                ->with('success', 'Đã cập nhật Role.');
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:255',
                'unique:admin_roles,slug,' . $role->id,
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'permissions' => [
                'nullable',
                'array',
            ],

            'permissions.*' => [
                'integer',
                'exists:admin_permissions,id',
            ],
        ], [
            'name.required' => 'Vui lòng nhập tên Role.',
            'slug.unique' => 'Slug này đã tồn tại.',
        ]);

        $slug = $validated['slug']
            ?? Str::slug($validated['name']);

        // Nếu tự sinh slug thì vẫn phải kiểm tra trùng
        $slugExists = AdminRole::where('slug', $slug)
            ->where('id', '!=', $role->id)
            ->exists();

        if ($slugExists) {
            return back()
                ->withInput()
                ->withErrors([
                    'slug' => 'Slug đã tồn tại. Vui lòng chọn tên khác.',
                ]);
        }

        $role->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
        ]);

        $role->permissions()->sync(
            $validated['permissions'] ?? []
        );

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Đã cập nhật Role và Permission.');
    }


    /**
     * Xóa Role
     */
    public function destroy(AdminRole $role)
    {
        // Không cho xóa Super Admin
        if ($role->is_super_admin) {
            return back()->with(
                'error',
                'Không thể xóa Role Super Admin.'
            );
        }

        // Không cho xóa Role đang được Staff sử dụng
        if ($role->admins()->exists()) {
            return back()->with(
                'error',
                'Không thể xóa Role đang được gán cho Staff.'
            );
        }

        $role->permissions()->detach();

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with(
                'success',
                'Đã xóa Role thành công.'
            );
    }
}
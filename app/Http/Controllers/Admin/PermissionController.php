<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AdminPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    /**
     * Danh sách Permission
     */
    public function index(Request $request)
    {
        $query = AdminPermission::query()
            ->withCount('roles');

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%')
                    ->orWhere('module', 'like', '%' . $search . '%');
            });
        }

        // Lọc module
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        $permissions = $query
            ->orderBy('module')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $modules = AdminPermission::query()
            ->select('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');

        $totalPermissions = AdminPermission::count();

        $totalModules = AdminPermission::query()
            ->distinct('module')
            ->count('module');

        return view(
            'admin.pages.permissions.index',
            compact(
                'permissions',
                'modules',
                'totalPermissions',
                'totalModules'
            )
        );
    }


    /**
     * Form thêm Permission
     */
    public function create()
    {
        return view('admin.pages.permissions.create');
    }


    /**
     * Lưu Permission
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
                'required',
                'string',
                'max:255',
                'unique:admin_permissions,slug',
            ],

            'module' => [
                'required',
                'string',
                'max:100',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ], [
            'name.required' => 'Vui lòng nhập tên Permission.',

            'slug.required' => 'Vui lòng nhập Slug.',
            'slug.unique' => 'Slug Permission đã tồn tại.',

            'module.required' => 'Vui lòng nhập Module.',

            'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',
        ]);

        $validated['slug'] = Str::slug(
            $validated['slug'],
            '.'
        );

        // Kiểm tra lại slug sau khi chuẩn hóa
        if (
            AdminPermission::where(
                'slug',
                $validated['slug']
            )->exists()
        ) {
            return back()
                ->withErrors([
                    'slug' => 'Slug Permission đã tồn tại.'
                ])
                ->withInput();
        }

        AdminPermission::create($validated);

        return redirect()
            ->route('admin.permissions')
            ->with(
                'success',
                'Đã tạo Permission thành công.'
            );
    }


    /**
     * Xem chi tiết Permission
     */
    public function show(AdminPermission $permission)
    {
        $permission->load([
            'roles' => function ($query) {
                $query->withCount('admins')
                    ->orderBy('name');
            }
        ]);

        return view(
            'admin.pages.permissions.show',
            compact('permission')
        );
    }


    /**
     * Form sửa Permission
     */
    public function edit(AdminPermission $permission)
    {
        return view(
            'admin.pages.permissions.edit',
            compact('permission')
        );
    }


    /**
     * Cập nhật Permission
     */
    public function update(
        Request $request,
        AdminPermission $permission
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique(
                    'admin_permissions',
                    'slug'
                )->ignore($permission->id),
            ],

            'module' => [
                'required',
                'string',
                'max:100',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ], [
            'name.required' => 'Vui lòng nhập tên Permission.',

            'slug.required' => 'Vui lòng nhập Slug.',
            'slug.unique' => 'Slug Permission đã tồn tại.',

            'module.required' => 'Vui lòng nhập Module.',

            'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',
        ]);

        $validated['slug'] = Str::slug(
            $validated['slug'],
            '.'
        );

        // Nếu slug sau chuẩn hóa bị trùng
        $exists = AdminPermission::query()
            ->where('slug', $validated['slug'])
            ->where('id', '!=', $permission->id)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors([
                    'slug' => 'Slug Permission đã tồn tại.'
                ])
                ->withInput();
        }

        $permission->update($validated);

        return redirect()
            ->route(
                'admin.permissions.show',
                $permission
            )
            ->with(
                'success',
                'Đã cập nhật Permission thành công.'
            );
    }


    /**
     * Xóa Permission
     */
    public function destroy(AdminPermission $permission)
    {
        /*
         * Không cho xóa Permission
         * đang được Role sử dụng.
         */
        $rolesCount = $permission->roles()->count();

        if ($rolesCount > 0) {
            return back()->with(
                'error',
                "Không thể xóa Permission này vì đang được {$rolesCount} Role sử dụng."
            );
        }

        $permission->delete();

        return redirect()
            ->route('admin.permissions')
            ->with(
                'success',
                'Đã xóa Permission thành công.'
            );
    }
}
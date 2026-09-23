<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AdminPermission;
use App\Models\AdminRole;
use Illuminate\Http\Request;

class MatrixController extends Controller
{
    /**
     * Ma trận Role × Module
     */
    public function index()
    {
        $roles = AdminRole::query()
            ->with('permissions')
            ->orderBy('name')
            ->get();

        $permissions = AdminPermission::query()
            ->orderBy('slug')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Gom permission theo module
        |--------------------------------------------------------------------------
        |
        | movies.view
        | movies.create
        | movies.edit
        | movies.delete
        |
        | =>
        |
        | movies
        |   - view
        |   - create
        |   - edit
        |   - delete
        |
        */

        $modules = $permissions
            ->groupBy(function ($permission) {
                return explode('.', $permission->slug)[0];
            })
            ->map(function ($modulePermissions) {
                return $modulePermissions
                    ->sortBy(function ($permission) {
                        return $this->permissionOrder(
                            explode('.', $permission->slug)[1] ?? ''
                        );
                    })
                    ->values();
            });

        /*
        |--------------------------------------------------------------------------
        | Chuẩn bị trạng thái từng Role / Module
        |--------------------------------------------------------------------------
        */

        $matrix = [];

        foreach ($roles as $role) {

            $rolePermissionIds = $role->permissions
                ->pluck('id')
                ->toArray();

            foreach ($modules as $module => $modulePermissions) {

                $total = $modulePermissions->count();

                $granted = $modulePermissions
                    ->filter(function ($permission) use ($rolePermissionIds) {
                        return in_array(
                            $permission->id,
                            $rolePermissionIds
                        );
                    });

                $grantedCount = $granted->count();

                if ($grantedCount === 0) {
                    $status = 'none';
                } elseif ($grantedCount === $total) {
                    $status = 'full';
                } else {
                    $status = 'partial';
                }

                $matrix[$role->id][$module] = [
                    'status' => $status,

                    'total' => $total,

                    'granted' => $grantedCount,

                    'permissions' => $modulePermissions,

                    'granted_permissions' => $granted,

                    'missing_permissions' => $modulePermissions
                        ->filter(function ($permission) use ($rolePermissionIds) {
                            return !in_array(
                                $permission->id,
                                $rolePermissionIds
                            );
                        })
                        ->values(),
                ];
            }
        }

        return view('admin.pages.matrix.index', compact(
            'roles',
            'modules',
            'matrix'
        ));
    }

    /**
     * Thứ tự hiển thị action trong module.
     */
    private function permissionOrder(string $action): int
    {
        return match ($action) {
            'view' => 1,
            'create' => 2,
            'edit', 'update' => 3,
            'delete' => 4,
            'approve' => 5,
            'ban' => 6,
            'unban' => 7,
            'handle' => 8,
            'manage' => 9,
            default => 99,
        };
    }

    /**
     * Cập nhật quyền cho một role.
     */
    public function update(Request $request, AdminRole $role)
    {
        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [
                'integer',
                'exists:admin_permissions,id',
            ],
        ]);

        $role->permissions()->sync(
            $validated['permissions'] ?? []
        );

        return back()->with(
            'success',
            "Đã cập nhật quyền cho role {$role->name}."
        );
    }
}
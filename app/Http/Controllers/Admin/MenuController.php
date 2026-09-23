<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    /**
     * Danh sách menu
     */
    public function index()
    {
        $menus = Menu::query()
            ->with([
                'children' => function ($query) {
                    $query->orderBy('sort_order');
                }
            ])
            ->whereNull('parent_id')
            ->orderBy('location')
            ->orderBy('sort_order')
            ->get();

        $menusByLocation = [
            'main' => $menus->where('location', 'main'),
            'footer' => $menus->where('location', 'footer'),
            'mobile' => $menus->where('location', 'mobile'),
        ];

        return view(
            'admin.pages.menus.index',
            compact('menusByLocation')
        );
    }

    /**
     * Form thêm menu
     */
    public function create()
    {
        $parents = Menu::query()
            ->whereNull('parent_id')
            ->orderBy('location')
            ->orderBy('sort_order')
            ->get();

        return view(
            'admin.pages.menus.create',
            compact('parents')
        );
    }

    /**
     * Lưu menu
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'url' => [
                'nullable',
                'string',
                'max:500',
            ],

            'location' => [
                'required',
                Rule::in([
                    'main',
                    'footer',
                    'mobile',
                ]),
            ],

            'parent_id' => [
                'nullable',
                'integer',
                'exists:menus,id',
            ],

            'icon' => [
                'nullable',
                'string',
                'max:100',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ], [
            'name.required' => 'Vui lòng nhập tên menu.',
            'name.max' => 'Tên menu không được vượt quá 255 ký tự.',
            'location.required' => 'Vui lòng chọn vị trí menu.',
            'location.in' => 'Vị trí menu không hợp lệ.',
            'parent_id.exists' => 'Menu cha không tồn tại.',
        ]);

        /*
         * Nếu có menu cha thì menu cha phải
         * thuộc cùng location.
         */
        if (!empty($validated['parent_id'])) {
            $parent = Menu::find($validated['parent_id']);

            if (!$parent || $parent->location !== $validated['location']) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'parent_id' => 'Menu cha phải thuộc cùng vị trí menu.',
                    ]);
            }
        }

        Menu::create([
            'name' => $validated['name'],
            'url' => $validated['url'] ?? null,
            'location' => $validated['location'],
            'parent_id' => $validated['parent_id'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.menus.index')
            ->with('success', 'Đã thêm menu thành công.');
    }

    /**
     * Form sửa menu
     */
    public function edit(Menu $menu)
    {
        $parents = Menu::query()
            ->whereNull('parent_id')
            ->where('id', '!=', $menu->id)
            ->where('location', $menu->location)
            ->orderBy('sort_order')
            ->get();

        return view(
            'admin.pages.menus.edit',
            compact('menu', 'parents')
        );
    }

    /**
     * Cập nhật menu
     */
    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'url' => [
                'nullable',
                'string',
                'max:500',
            ],

            'location' => [
                'required',
                Rule::in([
                    'main',
                    'footer',
                    'mobile',
                ]),
            ],

            'parent_id' => [
                'nullable',
                'integer',
                'exists:menus,id',
            ],

            'icon' => [
                'nullable',
                'string',
                'max:100',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        /*
         * Không cho menu làm cha của chính nó.
         */
        if (
            !empty($validated['parent_id']) &&
            (int) $validated['parent_id'] === (int) $menu->id
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'parent_id' => 'Menu không thể làm menu cha của chính nó.',
                ]);
        }

        /*
         * Kiểm tra menu cha cùng location.
         */
        if (!empty($validated['parent_id'])) {
            $parent = Menu::find($validated['parent_id']);

            if (!$parent || $parent->location !== $validated['location']) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'parent_id' => 'Menu cha phải thuộc cùng vị trí menu.',
                    ]);
            }
        }

        $menu->update([
            'name' => $validated['name'],
            'url' => $validated['url'] ?? null,
            'location' => $validated['location'],
            'parent_id' => $validated['parent_id'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.menus.index')
            ->with('success', 'Đã cập nhật menu thành công.');
    }

    /**
     * Xóa menu
     */
    public function destroy(Menu $menu)
    {
        /*
         * Nếu menu có menu con thì không cho xóa trực tiếp.
         */
        if ($menu->children()->exists()) {
            return back()->with(
                'error',
                'Không thể xóa menu đang có menu con.'
            );
        }

        $menu->delete();

        return back()->with(
            'success',
            'Đã xóa menu thành công.'
        );
    }

    /**
     * Bật / tắt menu
     */
    public function toggle(Menu $menu)
    {
        $menu->update([
            'is_active' => !$menu->is_active,
        ]);

        return back()->with(
            'success',
            $menu->is_active
                ? 'Đã bật menu.'
                : 'Đã tắt menu.'
        );
    }
}
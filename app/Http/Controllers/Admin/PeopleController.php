<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PeopleController extends Controller
{
    /**
     * Danh sách diễn viên / đạo diễn
     */
    public function index()
    {
        $people = Person::withCount('movies')
            ->orderBy('name')
            ->paginate(15);

        return view(
            'admin.pages.people.people',
            compact('people')
        );
    }


    /**
     * Form thêm người
     */
    public function create()
    {
        return view('admin.pages.people.add');
    }


    /**
     * Lưu người mới
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
                'unique:people,slug',
            ],

            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'biography' => [
                'nullable',
                'string',
            ],
        ], [
            'name.required' => 'Vui lòng nhập tên.',
            'slug.unique' => 'Slug này đã tồn tại.',
            'avatar.image' => 'File phải là hình ảnh.',
            'avatar.mimes' => 'Ảnh phải có định dạng JPG, JPEG, PNG hoặc WEBP.',
            'avatar.max' => 'Ảnh không được vượt quá 2MB.',
        ]);


        if (empty($validated['slug'])) {
             $validated['slug'] = $this->makeUniqueSlug(
                Person::class,
                $validated['name']
            );
        } else {
            $validated['slug'] = Str::slug(
                $validated['slug']
            );
        }


        // Upload avatar
        if ($request->hasFile('avatar')) {

            $validated['avatar'] = $request
                ->file('avatar')
                ->store('people', 'public');
        }


        Person::create($validated);


        return redirect()
            ->route('admin.people')
            ->with('success', 'Thêm người thành công.');
    }

    /**
     * Form sửa người
     */
    public function edit($id)
    {
        $person = Person::findOrFail($id);

        return view(
            'admin.pages.people.edit',
            compact('person')
        );
    }


    /**
     * Cập nhật người
     */
    public function update(Request $request, $id)
    {
        $person = Person::findOrFail($id);


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
                'unique:people,slug,' . $person->id,
            ],

            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'biography' => [
                'nullable',
                'string',
            ],
        ], [
            'name.required' => 'Vui lòng nhập tên.',
            'slug.unique' => 'Slug này đã tồn tại.',
            'avatar.image' => 'File phải là hình ảnh.',
            'avatar.mimes' => 'Ảnh phải có định dạng JPG, JPEG, PNG hoặc WEBP.',
            'avatar.max' => 'Ảnh không được vượt quá 2MB.',
        ]);


        if (empty($validated['slug'])) {

            $validated['slug'] = $this->makeUniqueSlug(
                Person::class,
                $validated['name'],
                $country->id
            );

        } else {

            $validated['slug'] = Str::slug(
                $validated['slug']
            );
        }


        // Nếu có upload ảnh mới
        if ($request->hasFile('avatar')) {

            // Xóa ảnh cũ
            if (
                $person->avatar &&
                Storage::disk('public')->exists($person->avatar)
            ) {
                Storage::disk('public')->delete(
                    $person->avatar
                );
            }


            // Lưu ảnh mới
            $validated['avatar'] = $request
                ->file('avatar')
                ->store('people', 'public');
        }


        $person->update($validated);


        return redirect()
            ->route('admin.people')
            ->with('success', 'Cập nhật người thành công.');
    }


    /**
     * Xóa người
     */
    public function destroy($id)
    {
        $person = Person::findOrFail($id);

        /*
         * movie_people có FK cascadeOnDelete
         * nên khi xóa person,
         * các liên kết với phim cũng sẽ được xóa.
         */
        $person->delete();


        return redirect()
            ->route('admin.people')
            ->with('success', 'Xóa người thành công.');
    }

}
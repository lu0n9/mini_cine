<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::withCount('movies')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.pages.country.countries', compact('countries'));
    }

    public function create()
    {
        return view('admin.pages.country.add');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:countries,name',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:255',
                'unique:countries,slug',
            ],

            'code' => [
                'required',
                'string',
                'max:10',
                'unique:countries,code',
            ],
        ], [
            'name.required' => 'Vui lòng nhập tên quốc gia.',
            'name.unique' => 'Quốc gia này đã tồn tại.',

            'slug.unique' => 'Slug này đã tồn tại.',

            'code.required' => 'Vui lòng nhập mã quốc gia.',
            'code.unique' => 'Mã quốc gia này đã tồn tại.',
        ]);

   
        // Tự tạo slug nếu bỏ trống
        if (empty($validated['slug'])) {

            $validated['slug'] = $this->makeUniqueSlug(
                Country::class,
                $validated['name']
            );

        } else {

            $validated['slug'] = Str::slug(
                $validated['slug']
            );
        }


        // Chuẩn hóa mã quốc gia
        $validated['code'] = strtoupper(
            trim($validated['code'])
        );
  

        Country::create($validated);


        return redirect()
            ->route('admin.countries')
            ->with('success', 'Thêm quốc gia thành công.');
    }


    public function edit($id)
    {
        $country = Country::findOrFail($id);

        return view(
            'admin.pages.country.edit',
            compact('country')
        );
    }

    public function update(Request $request, $id)
    {
        $country = Country::findOrFail($id);


        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:countries,name,' . $country->id,
            ],

            'slug' => [
                'nullable',
                'string',
                'max:255',
                'unique:countries,slug,' . $country->id,
            ],

            'code' => [
                'required',
                'string',
                'max:10',
                'unique:countries,code,' . $country->id,
            ],
        ], [
            'name.required' => 'Vui lòng nhập tên quốc gia.',
            'name.unique' => 'Quốc gia này đã tồn tại.',

            'slug.unique' => 'Slug này đã tồn tại.',

            'code.required' => 'Vui lòng nhập mã quốc gia.',
            'code.unique' => 'Mã quốc gia này đã tồn tại.',
        ]);


        // Tạo slug nếu bỏ trống
        if (empty($validated['slug'])) {

            $validated['slug'] = $this->makeUniqueSlug(
                Country::class,
                $validated['name'],
                $country->id
            );

        } else {

            $validated['slug'] = Str::slug(
                $validated['slug']
            );
        }


        // Chuẩn hóa code
        $validated['code'] = strtoupper(
            trim($validated['code'])
        );


        $country->update($validated);


        return redirect()
            ->route('admin.countries')
            ->with('success', 'Cập nhật quốc gia thành công.');
    }


    public function destroy(Country $country)
    {
        $country->delete();

        return redirect()
            ->route('admin.countries')
            ->with('success', 'Xóa quốc gia thành công.');
    }

}
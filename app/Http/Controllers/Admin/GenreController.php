<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CrudService;
use Illuminate\Support\Str;
use App\Models\Genre;

class GenreController extends Controller
{
    protected CrudService $crud;

    public function __construct(CrudService $crud)
    {
        $this->crud = $crud;
    }

    public function index(){
        $genres = $this->crud->all(
            Genre::class,
            withCount: ['movies'],
            orderBy: 'name'
        );
        return view('admin.pages.genre.genres', compact('genres'));
    }

    public function create()
    {
        return view('admin.pages.genre.add');
    }

    public function store(Request $request){
        $validated = $request -> validate ([
                'name' => [
                'required',
                'string',
                'max:255',
                'unique:genres,name',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:255',
            ]
        ], [
            'name.required' => 'Vui lòng nhập tên.',
            'slug.unique' => 'Slug này đã tồn tại.',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = $this->makeUniqueSlug(
                Genre::class,
                $validated['name'],
            );
        } else {
            $validated['slug'] = Str::slug(
                $validated['slug']
            );
        }

        $this->crud->create(
            Genre::class,
            $validated
        );
        return redirect()->route('admin.content.genres'); 
    }

    public function edit($id)
    {
        $genre = $this->crud->findOrFail(
            Genre::class,
            $id
        );

        return view(
            'admin.pages.genre.edit',
            compact('genre')
        );
    }

    public function update(Request $request, $id)
    {
        $genre = $this->crud->findOrFail(
            Genre::class,
            $id
        );

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:genres,name,' . $genre->id,
            ],

            'slug' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        // Logic riêng của genre
        if (empty($validated['slug'])) {
            $validated['slug'] = $this->makeUniqueSlug(
                $validated['name'],
                $genre->id
            );
        } else {
            $validated['slug'] = Str::slug(
                $validated['slug']
            );
        }

        // CRUD dùng service
        $this->crud->update(
            $genre,
            $validated
        );

        return redirect()
            ->route('admin.content.genres')
            ->with('success', 'Cập nhật quốc gia thành công.');
    }

    public function destroy($id)
    {
        $genre = $this->crud->findOrFail(
            Genre::class,
            $id
        );

        $this->crud->delete($genre);

        return redirect()
            ->route('admin.content.genres')
            ->with('success', 'Xóa thành công.');
    }
}

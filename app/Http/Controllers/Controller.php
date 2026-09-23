<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;

abstract class Controller
{
    protected function success(string $message)
    {
        return back()->with('success', $message);
    }
    protected function makeUniqueSlug($modelClass, $string, $id = null)
    {
        $slug = Str::slug($string);
        $originalSlug = $slug;
        $count = 1;

        while (
            $modelClass::where('slug', $slug)
                ->when($id !== null, function ($query) use ($id) {
                    $query->where('id', '!=', $id);
                })
                ->exists()
        ) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}

<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\Person;

class PeopleController extends Controller
{
    public function show(string $slug)
    {
        $person = Person::where('slug', $slug)
            ->with([
                'movies' => function ($query) {
                    $query
                        ->where('is_published', true)
                        ->orderByDesc('release_year');
                }
            ])
            ->firstOrFail();

        return view(
            'client.pages.people.show',
            compact('person')
        );
    }
}
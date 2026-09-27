@extends('client.layouts.master')

@section('title', $page->seo_title ?: $page->title)

@section('content')

<section class="static-page">

    <div class="static-page__container">

        {{-- Header --}}
        <header class="static-page__header">

            <h1 class="static-page__title">
                {{ $page->title }}
            </h1>

            @if($page->excerpt)
                <p class="static-page__excerpt">
                    {{ $page->excerpt }}
                </p>
            @endif

            <div class="static-page__meta">
                Cập nhật
                {{ $page->updated_at?->format('d/m/Y') }}
            </div>

        </header>

        {{-- Content --}}
        <article class="static-page__content">

            {!! $page->content !!}

        </article>

    </div>

</section>

@endsection
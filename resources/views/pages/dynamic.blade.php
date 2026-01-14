@extends('layouts.app')

@section('content')
    <div class="flex flex-col">

        <div class="w-full">
            <h2 class="w-full">
                <span class="px-4">{{ $menu->name }}</span>
            </h2>
        </div>

        <div class="page-content prose max-w-none text-slate-700 leading-relaxed">
            {!! $menu->content !!}
        </div>

    </div>
@endsection
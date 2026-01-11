@extends('layouts.app')

@section('content')
    <h1>{{ $menu->name }}</h1>

    <div class="page-content">
        {!! $menu->content !!}
    </div>
@endsection
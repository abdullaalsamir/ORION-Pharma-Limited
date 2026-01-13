@extends('layouts.app')

@section('content')

    <h2>{{ $menu->name }}</h2>

    <div class="page-content">
        {!! $menu->content !!}
    </div>

@endsection
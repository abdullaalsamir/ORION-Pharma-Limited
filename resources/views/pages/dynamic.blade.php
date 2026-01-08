@extends('layouts.app')

@section('content')
    <h1>{{ $menu->name }}</h1>

    {!! nl2br(e($menu->content)) !!}
@endsection
@extends('layouts.app')

@section('title', 'Sitemap')

@section('content')
    <div class="relative rounded-2xl border border-slate-200 bg-white p-8 space-y-10">

        @foreach($menus as $item)
            <div class="flex items-center gap-1 mb-2">
                <div class="w-10 h-10 flex items-center justify-center text-slate-400">
                    <i class="fas fa-bars"></i>
                </div>

                @if($item->children->count() > 0)
                    <div class="text-lg text-orion-blue font-semibold">
                        {{ $item->name }}
                    </div>
                @else
                    <a href="{{ url('/' . $item->full_slug) }}"
                        class="text-lg text-orion-blue font-semibold hover:text-orion-blue transition-colors">
                        {{ $item->name }}
                    </a>
                @endif
            </div>

            @if($item->children->count() > 0)
                <ul
                    class="space-y-3 relative before:absolute before:inset-y-0 before:left-5 before:w-px before:bg-orion-blue/50 ml-6">
                    @foreach($item->children as $child)
                        <li class="relative">
                            <div class="absolute left-5 top-1/2 -mt-px w-3 h-px bg-orion-blue/50"></div>

                            @if($child->children->count() > 0)
                                <span class="block pl-10 py-1 text-base font-semibold text-orion-blue">
                                    {{ $child->name }}
                                </span>
                            @else
                                <a href="{{ url('/' . $child->full_slug) }}"
                                    class="block pl-10 py-1 text-base text-slate-500 font-semibold hover:text-orion-blue transition-colors">
                                    {{ $child->name }}
                                </a>
                            @endif

                            @if($child->children->count() > 0)
                                <ul
                                    class="space-y-2 mt-2 relative before:absolute before:inset-y-0 before:left-[2.35rem] before:w-px before:bg-orion-blue/50">
                                    @foreach($child->children as $subchild)
                                        <li class="relative">
                                            <div class="absolute left-[2.35rem] top-1/2 -mt-px w-3 h-px bg-orion-blue/50"></div>
                                            <a href="{{ url('/' . $subchild->full_slug) }}"
                                                class="block pl-14 py-0.5 text-base font-semibold text-slate-400 hover:text-orion-blue transition-colors">
                                                {{ $subchild->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        @endforeach

    </div>
@endsection
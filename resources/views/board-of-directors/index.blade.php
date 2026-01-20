@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($items as $item)
                <div
                    class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-slate-100 flex flex-col">
                    <a href="{{ url($menu->full_slug . '/' . $item->slug) }}"
                        class="relative block aspect-[3/4] overflow-hidden bg-slate-100">
                        <img src="{{ url($menu->full_slug . '/' . basename($item->image_path)) }}"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    </a>

                    <div class="p-6 flex flex-col flex-grow text-center">
                        <h3 class="text-xl font-bold text-orion-blue mb-1 group-hover:text-blue-600 transition-colors">
                            {{ $item->name }}
                        </h3>
                        <p class="text-orion-blue font-semibold text-sm mb-6 opacity-80 uppercase tracking-wide">
                            {{ $item->designation }}
                        </p>

                        <div class="mt-auto pt-4 border-t border-slate-50">
                            <a href="{{ url($menu->full_slug . '/' . $item->slug) }}"
                                class="text-orion-blue font-bold text-sm inline-flex items-center gap-2 group/btn">
                                View Full Profile
                                <i
                                    class="fa-solid fa-arrow-right text-xs transition-transform group-hover/btn:translate-x-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
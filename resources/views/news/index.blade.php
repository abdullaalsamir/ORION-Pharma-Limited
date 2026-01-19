@extends('layouts.app')

@section('content')
    <div class="py-12">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($items as $item)
                <div
                    class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-slate-100 flex flex-col">
                    <a href="{{ url($menu->full_slug . '/' . $item->slug) }}"
                        class="relative block aspect-[16/9] overflow-hidden">
                        <img src="{{ url($menu->full_slug . '/' . basename($item->image_path)) }}"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        <div class="absolute top-4 left-4">
                            <span class="bg-orion-blue text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                                {{ $item->news_date->format('M Y') }}
                            </span>
                        </div>
                    </a>

                    <div class="p-6 flex flex-col flex-grow">
                        <h3
                            class="text-xl font-bold text-orion-blue mb-3 line-clamp-1 group-hover:text-blue-600 transition-colors">
                            {{ $item->title }}
                        </h3>
                        <p class="text-slate-600 text-sm line-clamp-2 mb-6 leading-relaxed">
                            {{ $item->description }}
                        </p>

                        <div class="mt-auto pt-4 border-t border-slate-50 flex justify-between items-center">
                            <span class="text-slate-400 text-xs flex items-center gap-1">
                                <i class="fa-regular fa-calendar"></i>
                                {{ $item->news_date->format('F d, Y') }}
                            </span>
                            <a href="{{ url($menu->full_slug . '/' . $item->slug) }}"
                                class="text-orion-blue font-bold text-sm flex items-center gap-2 group/btn">
                                Read More
                                <i
                                    class="fa-solid fa-arrow-right text-xs transition-transform group-hover/btn:translate-x-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-12">
            {{ $items->links() }}
        </div>
    </div>
@endsection
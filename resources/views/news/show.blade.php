@extends('layouts.app')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
        <nav class="flex items-center gap-2 text-sm font-medium text-slate-500">
            <a href="{{ url('/') }}" class="hover:text-orion-blue">Home</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <a href="{{ url($menu->full_slug) }}" class="hover:text-orion-blue">News List</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-orion-blue truncate max-w-50">{{ $item->title }}</span>
        </nav>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        <article class="lg:col-span-8">
            <div class="rounded-xl overflow-hidden mb-8 aspect-video shimmer relative border border-slate-200">
                <img src="{{ url($menu->full_slug . '/' . basename($item->file_path)) }}" alt="{{ $item->title }}"
                    class="product-image w-full h-full object-cover">
            </div>

            <div class="mb-8">
                <h2 class="text-3xl md:text-4xl font-bold text-orion-blue leading-tight mb-4">
                    {{ $item->title }}
                </h2>
                <div class="flex items-center gap-2 text-slate-500 font-semibold uppercase tracking-wider text-xs">
                    {{ $item->news_date->format('d F, Y') }}
                </div>
            </div>

            <div class="prose prose-lg max-w-none text-slate-700 leading-relaxed text-justify">
                {!! $item->description !!}
            </div>

            <div class="mt-12 pt-8 border-t border-slate-200 flex items-center gap-4">
                <span class="font-bold text-slate-700">Share this news:</span>
                <div class="flex gap-2">
                    <a href="#"
                        class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center hover:bg-orion-blue hover:text-white transition-all text-slate-500">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                    <a href="#"
                        class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center hover:bg-orion-blue hover:text-white transition-all text-slate-500">
                        <i class="fa-brands fa-linkedin-in"></i>
                    </a>
                </div>
            </div>
        </article>

        <aside class="lg:col-span-4">
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden sticky top-27.5">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                    <h4 class="text-base font-bold text-slate-900 flex items-center gap-3">
                        <span class="flex-none w-1 h-5 bg-orion-blue rounded-full"></span>
                        Recent News
                    </h4>
                </div>

                <div class="divide-y divide-slate-100">
                    @foreach($related as $rel)
                        @php
                            $isPdf = $rel->file_type === 'pdf';
                            $link = $isPdf
                                ? url($menu->full_slug . '/' . basename($rel->file_path))
                                : url($menu->full_slug . '/' . $rel->slug);
                        @endphp

                        <a href="{{ $link }}" @if($isPdf) target="_blank" @endif
                            class="group flex p-5 hover:bg-slate-50 transition-colors">

                            @if(!$isPdf)
                                <div class="w-20 h-14 rounded-lg overflow-hidden shrink-0 shimmer relative border border-slate-100">
                                    <img src="{{ url($menu->full_slug . '/' . basename($rel->file_path)) }}"
                                        class="product-image w-full h-full object-cover transition-transform duration-500">
                                </div>
                            @else
                                <div
                                    class="w-20 h-14 rounded-lg overflow-hidden bg-red-50 shrink-0 flex items-center justify-center border border-red-100 transition-colors group-hover:bg-red-100">
                                    <i class="fas fa-file-pdf text-xl text-red-500"></i>
                                </div>
                            @endif

                            <div class="ml-4">
                                <h5
                                    class="text-xs font-bold text-slate-800 line-clamp-2 group-hover:text-orion-blue transition-colors leading-snug mb-1">
                                    {{ $rel->title }}
                                </h5>
                                <div
                                    class="flex items-center gap-2 text-[10px] text-slate-400 font-semibold uppercase tracking-wider">
                                    {{ $rel->news_date->format('d F, Y') }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="p-4 bg-slate-50/50">
                    <a href="{{ url($menu->full_slug) }}"
                        class="flex items-center justify-center gap-2 w-full py-3 bg-white text-orion-blue text-xs font-bold uppercase tracking-widest rounded-lg border border-slate-200 hover:border-orion-blue hover:bg-orion-blue hover:text-white transition-all duration-300">
                        View All News
                        <i class="fa-solid fa-chevron-right text-[9px]"></i>
                    </a>
                </div>
            </div>
        </aside>
    </div>
@endsection
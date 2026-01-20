@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <nav class="flex items-center gap-2 text-sm font-medium text-slate-500">
                <a href="{{ url('/') }}" class="hover:text-orion-blue">Home</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <a href="{{ url($menu->full_slug) }}" class="hover:text-orion-blue">Board of Directors</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-orion-blue font-bold">{{ $item->name }}</span>
            </nav>
            <a href="{{ url($menu->full_slug) }}"
                class="text-slate-600 hover:text-orion-blue transition-colors flex items-center gap-2 font-semibold">
                <i class="fa-solid fa-circle-arrow-left"></i> Back to List
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <article class="lg:col-span-8">
                <div class="rounded-3xl overflow-hidden shadow-2xl mb-8 aspect-[3/4] max-w-md mx-auto bg-slate-200">
                    <img src="{{ url($menu->full_slug . '/' . basename($item->image_path)) }}" alt="{{ $item->name }}"
                        class="w-full h-full object-cover">
                </div>

                <div class="mb-8">
                    <h2 class="text-3xl md:text-4xl font-extrabold text-orion-blue leading-tight mb-2">
                        {{ $item->name }}
                    </h2>
                    <p class="text-xl font-bold text-orion-blue/70 uppercase tracking-widest">
                        {{ $item->designation }}
                    </p>
                </div>

                <div
                    class="prose prose-lg max-w-none text-slate-700 leading-relaxed whitespace-pre-line border-t border-slate-100 pt-8">
                    {!! $item->description !!}
                </div>

                <div class="mt-12 pt-8 border-t border-slate-200 flex items-center gap-4">
                    <span class="font-bold text-slate-700">Share profile:</span>
                    <div class="flex gap-2">
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all"><i
                                class="fa-brands fa-facebook-f"></i></a>
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center hover:bg-blue-700 hover:text-white transition-all"><i
                                class="fa-brands fa-linkedin-in"></i></a>
                    </div>
                </div>
            </article>

            <aside class="lg:col-span-4 space-y-8">
                <div class="bg-slate-100 rounded-3xl p-8 sticky top-[110px]">
                    <h4 class="text-xl font-bold text-orion-blue mb-6 flex items-center gap-2">
                        <span class="w-2 h-8 bg-orion-blue rounded-full"></span>
                        Our Leadership
                    </h4>

                    <div class="space-y-6">
                        @php
                            $others = \App\Models\BoardDirector::where('id', '!=', $item->id)->where('is_active', 1)->orderBy('order')->take(5)->get();
                        @endphp
                        @foreach($others as $other)
                            <a href="{{ url($menu->full_slug . '/' . $other->slug) }}" class="group flex items-start gap-4">
                                <div class="w-24 h-16 rounded-xl overflow-hidden flex-shrink-0 shadow-sm">
                                    <img src="{{ url($menu->full_slug . '/' . basename($other->image_path)) }}"
                                        class="w-full h-full object-cover transition-transform group-hover:scale-110">
                                </div>
                                <div>
                                    <h5
                                        class="text-sm font-bold text-slate-800 line-clamp-1 group-hover:text-orion-blue transition-colors">
                                        {{ $other->name }}
                                    </h5>
                                    <span class="text-[11px] font-bold text-slate-400 mt-1 block uppercase truncate">
                                        {{ $other->designation }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <a href="{{ url($menu->full_slug) }}"
                        class="mt-8 block text-center bg-white text-orion-blue font-bold py-3 rounded-xl border border-orion-blue/20 hover:bg-orion-blue hover:text-white transition-all duration-300">
                        View All Directors
                    </a>
                </div>
            </aside>
        </div>
    </div>
@endsection
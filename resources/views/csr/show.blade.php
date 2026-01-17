@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <nav class="flex items-center gap-2 text-sm font-medium text-slate-500">
                <a href="{{ url('/') }}" class="hover:text-orion-blue">Home</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <a href="{{ route('csr.index') }}" class="hover:text-orion-blue">CSR List</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-orion-blue truncate max-w-[200px]">{{ $item->title }}</span>
            </nav>
            <a href="{{ route('csr.index') }}"
                class="text-slate-600 hover:text-orion-blue transition-colors flex items-center gap-2 font-semibold">
                <i class="fa-solid fa-circle-arrow-left"></i> Back to List
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <article class="lg:col-span-8">
                <div class="rounded-3xl overflow-hidden shadow-2xl mb-8 aspect-[16/9] bg-slate-200">
                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}"
                        class="w-full h-full object-cover">
                </div>

                <div class="mb-8">
                    <h2 class="text-3xl md:text-4xl font-extrabold text-orion-blue leading-tight mb-4">
                        {{ $item->title }}
                    </h2>
                    <div class="flex items-center gap-6 text-slate-500">
                        <span class="flex items-center gap-2">
                            <i class="fa-regular fa-calendar-check text-orion-blue"></i>
                            {{ $item->csr_date->format('F d, Y') }}
                        </span>
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                        <span class="flex items-center gap-2 text-orion-blue font-semibold">
                            Social Impact
                        </span>
                    </div>
                </div>

                <div class="prose prose-lg max-w-none text-slate-700 leading-relaxed whitespace-pre-line">
                    {{ $item->description }}
                </div>

                <div class="mt-12 pt-8 border-t border-slate-200 flex items-center gap-4">
                    <span class="font-bold text-slate-700">Share this impact:</span>
                    <div class="flex gap-2">
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all"><i
                                class="fa-brands fa-facebook-f"></i></a>
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center hover:bg-blue-400 hover:text-white transition-all"><i
                                class="fa-brands fa-twitter"></i></a>
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
                        Recent Initiatives
                    </h4>

                    <div class="space-y-6">
                        @foreach($related as $rel)
                            <a href="{{ route('csr.show', $rel->id) }}" class="group flex items-start gap-4">
                                <div class="w-24 h-16 rounded-xl overflow-hidden flex-shrink-0 shadow-sm">
                                    <img src="{{ asset('storage/' . $rel->image_path) }}"
                                        class="w-full h-full object-cover transition-transform group-hover:scale-110">
                                </div>
                                <div>
                                    <h5
                                        class="text-sm font-bold text-slate-800 line-clamp-2 group-hover:text-orion-blue transition-colors">
                                        {{ $rel->title }}
                                    </h5>
                                    <span class="text-[11px] text-slate-400 mt-1 block">
                                        {{ $rel->csr_date->format('M d, Y') }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <a href="{{ route('csr.index') }}"
                        class="mt-8 block text-center bg-white text-orion-blue font-bold py-3 rounded-xl border border-orion-blue/20 hover:bg-orion-blue hover:text-white transition-all duration-300">
                        View All Stories
                    </a>
                </div>
            </aside>
        </div>
    </div>
@endsection
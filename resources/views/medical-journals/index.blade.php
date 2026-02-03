@extends('layouts.app')

@section('content')
    <div class="medical-journals-page pb-10"
        x-data="{ selectedYear: null, selectYear(year) { const wrapper = '#journals-smooth-wrapper'; animateHeight(wrapper, () => { this.selectedYear = year }); this.$nextTick(() => animateHeight(wrapper, () => {})); }, clearYear() { const wrapper = '#journals-smooth-wrapper'; animateHeight(wrapper, () => { this.selectedYear = null }); this.$nextTick(() => animateHeight(wrapper, () => {})); } }">

        <div id="journals-smooth-wrapper" class="smooth-container">

            <div x-show="!selectedYear"
                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 xl:grid-cols-7 gap-6 tr-slide-in pt-2">

                @foreach($groupedJournals as $year => $journals)
                    <button @click="selectYear({{ $year }})"
                        class="group aspect-square w-full border border-slate-200 rounded-2xl bg-white transition-all duration-300 hover:border-orion-blue hover:-translate-y-0.5 cursor-pointer overflow-hidden">

                        <div class="w-full h-full p-8 flex flex-col items-center justify-center">
                            <i class="fa-solid fa-book text-4xl text-orion-blue mb-4"></i>
                            <span class="text-lg font-bold text-slate-700 group-hover:text-orion-blue">
                                {{ $year }}
                            </span>
                        </div>

                    </button>

                @endforeach
            </div>

            @foreach($groupedJournals as $year => $journals)
                <div x-show="selectedYear === {{ $year }}" style="display:none" x-init="$el.style.display = null"
                    class="tr-slide-in">

                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-1.5 h-8 bg-orion-blue rounded-full"></div>

                        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">
                            {{ $year }}
                        </h2>

                        <div class="flex-1 h-px bg-linear-to-r from-orion-blue/40 to-transparent"></div>

                        <button @click="clearYear()" class="text-slate-400 hover:text-rose-500 cursor-pointer transition-colors"
                            title="Close">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>

                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                        <div class="divide-y divide-slate-100">
                            @foreach($journals as $index => $j)
                                <div
                                    class="group flex flex-col sm:flex-row items-center gap-6 px-6 py-4 transition-all duration-300">

                                    <div class="flex items-center gap-5 shrink-0">
                                        <span class="text-sm text-slate-300">
                                            {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                        </span>
                                        <div class="text-rose-500 flex items-center justify-center">
                                            <i class="fa-solid fa-file-pdf text-3xl"></i>
                                        </div>
                                    </div>

                                    <div class="grow text-center sm:text-left">
                                        <h3
                                            class="text-base font-semibold text-slate-700 group-hover:text-orion-blue transition-colors leading-snug">
                                            {{ $j->title }}
                                        </h3>
                                    </div>

                                    <div class="flex items-center gap-3 shrink-0">
                                        <a href="{{ url($menu->full_slug . '/' . $j->year . '/' . $j->filename) }}" target="_blank"
                                            class="flex items-center gap-2 px-4 py-2 rounded-lg bg-white text-orion-blue hover:bg-orion-blue hover:text-white font-bold text-xs transition-all duration-300 border border-slate-200 hover:border-orion-blue">
                                            <i class="fa-solid fa-eye text-[10px]"></i>
                                            View
                                        </a>

                                        <a href="{{ url($menu->full_slug . '/' . $j->year . '/' . $j->filename) }}"
                                            download="{{ $j->title }}.pdf"
                                            class="w-9 h-9 rounded-lg bg-white text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-all duration-300 border border-slate-100">
                                            <i class="fa-solid fa-download text-xs"></i>
                                        </a>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            @endforeach

        </div>
    </div>
@endsection
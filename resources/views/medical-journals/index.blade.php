@extends('layouts.app')

@section('content')
    <div class="py-16 bg-slate-50 min-h-screen">

        <div class="max-w-5xl mx-auto px-4">
            @forelse($groupedJournals as $year => $journals)
                <div class="mb-16 journal-year-block">

                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-2 h-10 bg-orion-blue rounded-full"></div>
                        <h2 class="text-3xl font-black text-slate-800 tracking-tight">{{ $year }}</h2>
                        <div class="flex-grow h-px bg-slate-200"></div>
                    </div>

                    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr class="bg-slate-50/80 border-b border-slate-100">
                                        <th
                                            class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] text-center w-20">
                                            SL</th>
                                        <th
                                            class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] text-left border-l border-slate-100">
                                            Journal Title</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @foreach($journals as $index => $j)
                                        <tr class="hover:bg-blue-50/40 transition-colors duration-300 group">
                                            <td
                                                class="px-6 py-5 text-center font-bold text-slate-300 group-hover:text-orion-blue transition-colors">
                                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                            </td>

                                            <td class="px-6 py-5 border-l border-slate-100">
                                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                                    <h3
                                                        class="text-slate-700 font-bold text-base md:text-lg group-hover:text-slate-900 transition-colors leading-snug">
                                                        {{ $j->title }}
                                                    </h3>

                                                    <div class="flex items-center gap-3 shrink-0">
                                                        <a href="{{ url($menu->full_slug . '/' . $j->year . '/' . $j->filename) }}"
                                                            target="_blank"
                                                            class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-orion-blue hover:text-white flex items-center justify-center transition-all duration-300 shadow-sm"
                                                            title="View Online">
                                                            <i class="fa-solid fa-eye text-sm"></i>
                                                        </a>

                                                        <a href="{{ url($menu->full_slug . '/' . $j->year . '/' . $j->filename) }}"
                                                            download="{{ $j->title }}.pdf"
                                                            class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all duration-300 shadow-sm"
                                                            title="Download PDF">
                                                            <i class="fa-solid fa-download text-sm"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-24 bg-white rounded-[3rem] border-2 border-dashed border-slate-200">
                    <i class="fa-solid fa-book-medical text-slate-100 text-8xl mb-6"></i>
                    <p class="text-slate-400 font-bold text-xl uppercase tracking-widest">No Journals Found</p>
                </div>
            @endforelse
        </div>

    </div>

    <style>
        .journal-year-block {
            opacity: 0;
            transform: translateY(30px);
            animation: tableFadeIn 0.8s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }

        @keyframes tableFadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @for ($i = 1; $i <= 10; $i++)
            .journal-year-block:nth-child({{ $i }}) {
                animation-delay:
                    {{ $i * 0.15 }}
                    s;
            }

        @endfor .overflow-x-auto {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .overflow-x-auto::-webkit-scrollbar {
            height: 4px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
    </style>
@endsection
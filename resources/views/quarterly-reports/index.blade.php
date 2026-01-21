@extends('layouts.app')
@section('content')
    <div class="py-16 bg-slate-50 min-h-screen">
        <div class="max-w-5xl mx-auto px-4">
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
                                    Disclosures</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($items as $index => $j)
                                <tr class="hover:bg-blue-50/40 transition-colors duration-300 group">
                                    <td
                                        class="px-6 py-5 text-center font-bold text-slate-300 group-hover:text-orion-blue align-top">
                                        {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-5 border-l border-slate-100">
                                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                                            <div class="flex-grow">
                                                <h3
                                                    class="text-slate-700 font-bold text-base md:text-lg group-hover:text-slate-900 leading-snug">
                                                    {{ $j->title }}
                                                </h3>
                                                @if($j->description)
                                                    <p class="text-sm text-slate-500 mt-2 leading-relaxed">{{ $j->description }}</p>
                                                @endif
                                                <div
                                                    class="mt-3 text-[11px] font-bold text-orion-blue uppercase tracking-widest">
                                                    Publication Date: {{ $j->publication_date->format('d/m/Y') }}
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3 shrink-0 pt-1">
                                                <a href="{{ url($menu->full_slug . '/' . $j->filename) }}" target="_blank"
                                                    class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-orion-blue hover:text-white flex items-center justify-center transition-all shadow-sm"
                                                    title="View PDF">
                                                    <i class="fa-solid fa-eye text-sm"></i>
                                                </a>
                                                <a href="{{ url($menu->full_slug . '/' . $j->filename) }}"
                                                    download="{{ $j->title }}.pdf"
                                                    class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all shadow-sm"
                                                    title="Download">
                                                    <i class="fa-solid fa-download text-sm"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center py-20 text-slate-400 italic">No information available.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
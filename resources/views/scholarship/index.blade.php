@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="mb-10 text-center">
            <h1 class="w-full">
                <span class="px-4">Scholarship Recipients</span>
            </h1>
            <p class="text-slate-500 max-w-2xl mx-auto mt-2 italic">
                List of medical students supported by Orion Pharma Limited
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th
                                class="px-6 py-4 text-sm font-bold text-orion-blue uppercase tracking-wider border-r border-slate-200 w-16 text-center">
                                SL</th>
                            <th
                                class="px-6 py-4 text-sm font-bold text-orion-blue uppercase tracking-wider border-r border-slate-200 w-80">
                                Name, Session & Roll</th>
                            <th
                                class="px-6 py-4 text-sm font-bold text-orion-blue uppercase tracking-wider border-r border-slate-200">
                                Name of Medical College</th>
                            <th
                                class="px-6 py-4 text-sm font-bold text-orion-blue uppercase tracking-wider w-32 text-center">
                                Photo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($items as $index => $item)
                            <tr class="hover:bg-blue-50/30 transition-colors duration-200 group">
                                <td class="px-6 py-4 text-center font-bold text-slate-400 border-r border-slate-100 align-top">
                                    {{ $index + 1 }}
                                </td>

                                <td class="px-6 py-4 border-r border-slate-100 align-top">
                                    <div class="font-bold text-slate-900 text-lg group-hover:text-orion-blue transition-colors">
                                        {{ $item->name }}
                                    </div>
                                    <div class="mt-1 space-y-0.5">
                                        @if($item->session)
                                            <div class="text-sm text-slate-500 font-medium">
                                                <span class="text-slate-400">Session:</span>
                                                {{ str_replace('Session: ', '', $item->session) }}
                                            </div>
                                        @endif
                                        @if($item->roll_no)
                                            <div class="text-sm text-slate-500 font-medium">
                                                <span class="text-slate-400">Roll No:</span>
                                                {{ str_replace('Roll No: ', '', $item->roll_no) }}
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4 border-r border-slate-100 align-top">
                                    <div class="text-slate-700 font-semibold leading-relaxed">
                                        {{ $item->medical_college }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 align-top">
                                    <div class="flex justify-center">
                                        <div
                                            class="relative w-20 aspect-[9/11] rounded-lg overflow-hidden ring-2 ring-slate-100 group-hover:ring-orion-blue/20 transition-all shadow-sm">
                                            <img src="{{ url($menu->full_slug . '/' . basename($item->image_path)) }}"
                                                alt="{{ $item->name }}" class="w-full h-full object-cover">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fa-solid fa-users-slash text-slate-200 text-5xl mb-4"></i>
                                        <p class="text-slate-400 font-medium italic">No recipients found at this time.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 text-center">
            <p class="text-[11px] text-slate-400 font-bold uppercase tracking-[0.2em]">ORION PHARMA LIMITED - CSR INITIATIVE
            </p>
        </div>
    </div>

    <style>
        .overflow-x-auto {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .overflow-x-auto::-webkit-scrollbar {
            height: 6px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 10px;
        }

        tbody tr {
            animation: slideIn 0.4s ease-out forwards;
            opacity: 0;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @for ($i = 1; $i <= 30; $i++)
            tbody tr:nth-child({{ $i }}) {
                animation-delay:
                    {{ $i * 0.05 }}
                    s;
            }

        @endfor
    </style>
@endsection
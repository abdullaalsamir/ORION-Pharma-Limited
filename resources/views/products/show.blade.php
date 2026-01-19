@extends('layouts.app')

@section('content')
<div class="py-12">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm font-medium text-slate-500 mb-8">
        <a href="{{ url('/') }}" class="hover:text-orion-blue">Home</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <a href="{{ url($menu->full_slug) }}" class="hover:text-orion-blue">Products</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-orion-blue font-bold">{{ $product->trade_name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        <!-- Left: Product Identity -->
        <div class="lg:col-span-4">
            <div class="sticky top-[110px] space-y-6">
                <div class="rounded-3xl overflow-hidden shadow-xl border-4 border-white aspect-video">
                    <img src="{{ url('products/' . $product->generic->slug . '/' . basename($product->image_path)) }}" alt="{{ $product->trade_name }}" class="w-full h-full object-cover">
                </div>
                
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
                    <h1 class="!text-left !my-0 !flex-none text-3xl font-black text-orion-blue mb-2">{{ $product->trade_name }}</h1>
                    <p class="text-lg text-slate-500 font-medium italic border-b border-slate-100 pb-4 mb-4">{{ $product->preparation }}</p>
                    
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-dna text-orion-blue mt-1"></i>
                            <div>
                                <span class="block text-[10px] uppercase font-bold text-slate-400">Generic Name</span>
                                <span class="font-bold text-slate-700">{{ $product->generic->name }}</span>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-stethoscope text-orion-blue mt-1"></i>
                            <div>
                                <span class="block text-[10px] uppercase font-bold text-slate-400">Therapeutic Class</span>
                                <span class="font-bold text-slate-700">{{ $product->therapeutic_class ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Technical Details -->
        <div class="lg:col-span-8">
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                <!-- Dynamic Tab System -->
                @php
                    $groups = [
                        'Clinical' => [
                            'indications' => 'Indications',
                            'dosage_admin' => 'Dosage & Administration',
                            'contraindications' => 'Contraindications',
                            'overdosage' => 'Overdosage'
                        ],
                        'Safety' => [
                            'precautions' => 'Precautions',
                            'side_effects' => 'Side Effects',
                            'drug_interactions' => 'Drug Interactions',
                            'use_children' => 'Use in Children',
                            'use_pregnancy_lactation' => 'Use in Pregnancy & Lactation',
                            'high_risk' => 'High Risk Groups'
                        ],
                        'Commercial' => [
                            'presentation' => 'Presentation',
                            'how_supplied' => 'How Supplied',
                            'commercial_pack' => 'Commercial Pack',
                            'packaging' => 'Packaging',
                            'storage' => 'Storage',
                            'official_specification' => 'Official Specification'
                        ]
                    ];
                @endphp

                <div class="flex border-b border-slate-100 overflow-x-auto bg-slate-50/50">
                    @foreach($groups as $groupName => $fields)
                        <button onclick="switchTab('{{ $groupName }}')" 
                                id="tab-btn-{{ $groupName }}"
                                class="tab-btn px-8 py-5 text-sm font-bold uppercase tracking-wider transition-all {{ $loop->first ? 'text-orion-blue border-b-2 border-orion-blue bg-white' : 'text-slate-400' }}">
                            {{ $groupName }}
                        </button>
                    @endforeach
                </div>

                <div class="p-8 md:p-12">
                    @foreach($groups as $groupName => $fields)
                        <div id="tab-content-{{ $groupName }}" class="tab-pane {{ $loop->first ? '' : 'hidden' }} space-y-10">
                            @foreach($fields as $key => $label)
                                @if(!empty($product->$key))
                                    <div class="relative pl-6">
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-orion-blue/10 rounded-full"></div>
                                        <h4 class="text-orion-blue font-black uppercase text-xs tracking-widest mb-3">{{ $label }}</h4>
                                        <div class="text-slate-700 leading-relaxed whitespace-pre-line text-[15px]">
                                            {{ $product->$key }}
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function switchTab(name) {
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('text-orion-blue', 'border-b-2', 'border-orion-blue', 'bg-white');
            b.classList.add('text-slate-400');
        });

        document.getElementById('tab-content-' + name).classList.remove('hidden');
        const activeBtn = document.getElementById('tab-btn-' + name);
        activeBtn.classList.add('text-orion-blue', 'border-b-2', 'border-orion-blue', 'bg-white');
        activeBtn.classList.remove('text-slate-400');
    }
</script>
@endsection
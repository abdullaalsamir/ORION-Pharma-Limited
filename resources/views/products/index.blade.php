@extends('layouts.app')

@section('content')
    <div class="py-12 bg-slate-50 min-h-screen">

        <!-- Sophisticated Filter Bar -->
        <div class="mb-10 sticky top-[100px] z-[80]">
            <div
                class="bg-white/80 backdrop-blur-md rounded-2xl shadow-sm border border-slate-200 p-4 flex flex-wrap items-center justify-between gap-6">

                <!-- Left: Toggle Group -->
                <div class="flex bg-slate-100 p-1 rounded-xl border border-slate-200">
                    <button onclick="setFilterMode('generic')" id="mode-generic"
                        class="filter-mode-btn active px-6 py-2 rounded-lg text-sm font-bold transition-all">
                        Generic
                    </button>
                    <button onclick="setFilterMode('trade')" id="mode-trade"
                        class="filter-mode-btn px-6 py-2 rounded-lg text-sm font-bold transition-all">
                        Trade
                    </button>
                </div>

                <!-- Right: Alphabet Row -->
                <div class="flex items-center gap-1 overflow-x-auto pb-2 md:pb-0 alphabet-container">
                    <button onclick="setLetter('all')" class="letter-btn active">All</button>
                    @foreach(range('A', 'Z') as $char)
                        <button onclick="setLetter('{{ $char }}')" class="letter-btn">{{ $char }}</button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Product Grid -->
        <div id="product-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @foreach($products as $p)
                <div class="product-card group bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 border border-slate-100"
                    data-generic="{{ strtoupper($p->generic->name) }}" data-trade="{{ strtoupper($p->trade_name) }}">

                    <a href="{{ url('products/' . $p->generic->slug . '/' . Str::slug($p->trade_name)) }}"
                        class="block aspect-video overflow-hidden bg-slate-50">
                        <img src="{{ url('products/' . $p->generic->slug . '/' . basename($p->image_path)) }}"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    </a>

                    <div class="p-6">
                        <span
                            class="text-[10px] font-bold text-orion-blue uppercase tracking-widest">{{ $p->generic->name }}</span>
                        <h3 class="text-xl font-bold text-slate-900 mb-4 line-clamp-1">{{ $p->trade_name }}</h3>
                        <a href="{{ url('products/' . $p->generic->slug . '/' . Str::slug($p->trade_name)) }}"
                            class="text-orion-blue font-bold text-sm flex items-center gap-2 group/link">
                            Specifications <i
                                class="fa-solid fa-arrow-right text-[10px] transition-transform group-hover/link:translate-x-1"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Empty State -->
        <div id="no-results" class="hidden py-20 text-center">
            <i class="fa-solid fa-box-open text-6xl text-slate-200 mb-4"></i>
            <p class="text-slate-500 font-medium">No products found matching your selection.</p>
        </div>
    </div>

    <style>
        .filter-mode-btn {
            color: #64748b;
        }

        .filter-mode-btn.active {
            background: #0054a6;
            color: white;
            box-shadow: 0 4px 12px rgba(0, 84, 166, 0.2);
        }

        .letter-btn {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-weight: 700;
            font-size: 13px;
            color: #64748b;
            transition: 0.2s;
        }

        .letter-btn:hover {
            background: #f1f5f9;
            color: #0054a6;
        }

        .letter-btn.active {
            background: #0054a6;
            color: white;
        }

        .alphabet-container::-webkit-scrollbar {
            height: 4px;
        }

        .product-card.hidden {
            display: none;
        }
    </style>

    <script>
        let filterMode = 'generic'; // Default
        let selectedLetter = 'all';

        function setFilterMode(mode) {
            filterMode = mode;
            document.querySelectorAll('.filter-mode-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('mode-' + mode).classList.add('active');
            applyFilter();
        }

        function setLetter(letter) {
            selectedLetter = letter;
            document.querySelectorAll('.letter-btn').forEach(b => b.classList.remove('active'));
            event.target.classList.add('active');
            applyFilter();
        }

        function applyFilter() {
            const cards = document.querySelectorAll('.product-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const compareVal = filterMode === 'generic' ? card.dataset.generic : card.dataset.trade;

                if (selectedLetter === 'all' || compareVal.startsWith(selectedLetter)) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });

            document.getElementById('no-results').classList.toggle('hidden', visibleCount > 0);
        }
    </script>
@endsection
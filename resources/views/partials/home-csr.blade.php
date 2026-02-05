@if(isset($csrItems) && $csrItems->isNotEmpty() && $csrMenu)
    <section class="mt-16">
        <h2 class="text-left text-orion-blue text-2xl uppercase font-bold mb-6">Corporate Social Responsibilities</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 grid-rows-1 overflow-hidden items-stretch">
            @foreach($csrItems as $item)
                <a href="{{ url($csrMenu->full_slug . '/' . $item->slug) }}"
                    class="index-card group bg-white rounded-xl overflow-hidden flex flex-col">

                    <div class="aspect-video overflow-hidden shimmer relative border-b border-slate-200">
                        <img src="{{ url($csrMenu->full_slug . '/' . basename($item->image_path)) }}"
                            class="product-image w-full h-full object-cover transition-transform duration-300"
                            alt="{{ $item->title }}">
                    </div>

                    <div class="p-6 flex flex-col grow">
                        <span class="text-[10px] font-bold text-orion-blue uppercase tracking-widest block mb-3">
                            {{ $item->csr_date->format('d F, Y') }}
                        </span>
                        <h3
                            class="text-lg font-bold text-slate-900 mb-3 group-hover:text-orion-blue transition-colors line-clamp-2">
                            {{ $item->title }}
                        </h3>
                        <p class="text-slate-600 text-sm leading-relaxed line-clamp-3">
                            {{ $item->description }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endif
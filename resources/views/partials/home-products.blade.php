@if(isset($homeProducts) && $homeProducts->isNotEmpty() && $productsMenu)
    <section class="mt-16">
        <h2 class="text-left text-orion-blue text-2xl uppercase font-bold mb-6">New Products</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 grid-rows-2 overflow-hidden items-stretch">
            @foreach($homeProducts as $p)
                <a href="{{ url('products/' . $p->generic->slug . '/' . Str::slug($p->trade_name)) }}"
                    class="index-card group bg-white rounded-xl overflow-hidden">

                    <div class="aspect-video border-b border-slate-200 overflow-hidden shimmer relative">
                        <img src="{{ url('products/' . $p->generic->slug . '/' . basename($p->image_path)) }}"
                            class="product-image w-full h-full object-cover transition-transform duration-300"
                            alt="{{ $p->trade_name }}">
                    </div>

                    <div class="p-6 flex flex-col grow">
                        <span class="text-[10px] font-bold text-orion-blue uppercase tracking-widest block mb-1">
                            {{ $p->generic->name }}
                        </span>
                        <h3 class="text-lg font-bold text-slate-900 group-hover:text-orion-blue transition-colors">
                            {{ $p->trade_name }}
                        </h3>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endif
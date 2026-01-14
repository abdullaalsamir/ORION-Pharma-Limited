<header class="fixed top-0 left-0 w-full z-[100]">
    <div class="relative z-50 bg-white border-b border-gray-200 shadow-sm">
        <div class="container mx-auto w-[90%] max-w-[1400px]">
            <div class="flex justify-between items-center h-[90px]">
                
                <div class="flex-shrink-0">
                    <a href="{{ url('/') }}" class="flex items-center">
                        <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="h-18 w-auto">
                    </a>
                </div>

                <nav class="hidden lg:flex h-full">
                    <ul class="flex h-full">
                        @foreach($menus as $menu)
                            @if($menu->isEffectivelyActive())
                                @php $hasChildren = $menu->children->count() > 0; @endphp

                                <li class="relative group flex items-center h-[90px]">
                                    <a href="{{ !$hasChildren ? url($menu->full_slug) : 'javascript:void(0)' }}" 
                                    class="relative z-50 px-3 h-full text-base font-semibold text-orion-blue group-hover:bg-orion-blue group-hover:text-white flex items-center gap-1 transition-all duration-200">
                                        {{ $menu->name }}
                                        @if($hasChildren)
                                            <i class="fa-solid fa-chevron-down text-[10px] transition-transform group-hover:rotate-180"></i>
                                        @endif
                                    </a>

                                    @if($hasChildren)
                                        <div class="absolute top-full left-0 w-64 pointer-events-none group-hover:pointer-events-auto" 
                                            style="clip-path: inset(0px -1000px -1000px -1000px);">
                                            
                                            <ul class="level-2-menu w-full bg-orion-blue shadow-xl py-0 border-t border-white/10 relative">
                                                @foreach($menu->children as $submenu)
                                                    @if($submenu->isEffectivelyActive())
                                                        @php $hasSub = $submenu->children->count() > 0; @endphp

                                                        <li class="relative group/sub">
                                                            <a href="{{ !$hasSub ? url($submenu->full_slug) : 'javascript:void(0)' }}" 
                                                            class="relative z-30 bg-orion-blue flex items-center justify-between px-6 py-3 text-base text-white group-hover/sub:bg-[#1a62ae] transition-colors border-b border-white/10">
                                                                {{ $submenu->name }}
                                                                @if($hasSub)
                                                                    <i class="fa-solid fa-chevron-right text-[10px] sub-chevron"></i>
                                                                @endif
                                                            </a>

                                                            @if($hasSub)
                                                                <ul class="level-3-menu absolute top-0 w-64 bg-[#1a62ae] shadow-xl py-0 opacity-0 invisible z-10">
                                                                    @foreach($submenu->children as $subsubmenu)
                                                                        <li class="border-b border-white/5 last:border-0">
                                                                            <a href="{{ url($subsubmenu->full_slug) }}" 
                                                                            class="block px-6 py-3 text-base text-white hover:bg-[#2576c7] transition-colors duration-200">
                                                                                {{ $subsubmenu->name }}
                                                                            </a>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </nav>

                <button class="lg:hidden p-2 text-orion-blue" onclick="document.getElementById('mobile-nav').classList.toggle('hidden')">
                    <i class="fa-solid fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
    </div>
    <div id="mobile-nav" class="hidden lg:hidden bg-white border-t border-gray-100 relative z-[60]">
        <div class="px-4 py-4 space-y-1">
            @foreach($menus as $menu)
                <a href="{{ url($menu->full_slug) }}" class="block px-3 py-2 text-base font-medium text-orion-blue hover:bg-gray-50">{{ $menu->name }}</a>
            @endforeach
        </div>
    </div>
</header>

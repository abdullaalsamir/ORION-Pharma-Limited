<div class="admin-card-header">
    <div class="flex flex-col">
        <h1>{{ $menu->name }}</h1>
        <p class="text-xs text-slate-400">Manage banners for this page</p>
    </div>

    <button onclick="openBannerUploadModal()" class="btn-success h-10!">
        <i class="fas fa-plus"></i> Add Image
    </button>
</div>

<div class="admin-card-body custom-scrollbar bg-slate-50/20">
    <div class="grid grid-cols-1 gap-4">
        @forelse($banners as $banner)
            <div
                class="relative group rounded-2xl border border-slate-200 bg-white p-1 hover:border-admin-blue transition-all">
                <div class="rounded-xl overflow-hidden bg-slate-100 relative">
                    <img src="{{ url($menu->full_slug . '/' . $banner->file_name) }}?v={{ time() }}"
                        class="w-full h-full object-cover transition-all duration-500 {{ !$banner->is_active ? 'opacity-40 grayscale' : '' }}"
                        alt="banner">

                    @if(!$banner->is_active)
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span
                                class="badge badge-danger bg-red-600! text-white! border-none! shadow-lg shadow-red-900/40">Inactive</span>
                        </div>
                    @endif
                </div>

                <div
                    class="absolute top-4 right-4 flex gap-2 opacity-0 group-hover:opacity-100 transition-all transform translate-y-2 group-hover:translate-y-0">
                    <button
                        class="w-8 h-8 rounded-lg bg-white/90 backdrop-blur shadow-xl flex items-center justify-center text-xs text-slate-600 hover:text-admin-blue hover:bg-white transition-all cursor-pointer"
                        onclick="openBannerEditModal({{ $banner->id }}, '{{ $banner->file_name }}', '{{ $menu->full_slug }}', {{ $banner->is_active }})">
                        <i class="fas fa-pencil"></i>
                    </button>
                    <button
                        class="w-8 h-8 rounded-lg bg-white/90 backdrop-blur shadow-xl flex items-center justify-center text-xs text-red-500 hover:bg-red-500 hover:text-white transition-all cursor-pointer"
                        onclick="deleteBannerImage({{ $banner->id }})">
                        <i class="fas fa-trash-can"></i>
                    </button>
                </div>
            </div>
        @empty
            <div
                class="flex flex-col items-center justify-center py-20 bg-white border-2 border-dashed border-slate-200 rounded-3xl text-slate-300">
                <i class="fas fa-cloud-arrow-up text-4xl mb-4"></i>
                <h2 class="text-slate-400!">No Banners Uploaded Yet</h2>
            </div>
        @endforelse
    </div>
</div>
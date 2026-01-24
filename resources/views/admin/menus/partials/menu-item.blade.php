@if($menu->slug === 'home') @php return; @endphp @endif

<li data-id="{{ $menu->id }}" class="nested-sortable-item group">
    <div
        class="flex items-center justify-between p-3.5 bg-white border border-slate-200 rounded-2xl hover:border-admin-blue transition-all">
        <div class="flex items-center gap-4">
            <div
                class="drag-handle cursor-grab active:cursor-grabbing p-1.5 text-slate-300 hover:text-admin-blue transition-colors">
                <i class="fas fa-grip-vertical"></i>
            </div>
            <span class="font-bold text-slate-700 text-sm tracking-tight">{{ $menu->name }}</span>
        </div>

        <div class="flex items-center gap-4">
            <span class="badge {{ $menu->is_multifunctional ? 'badge-purple' : 'badge-info' }}">
                {{ $menu->is_multifunctional ? 'Multi' : 'Func' }}
            </span>

            <span class="badge {{ $menu->isEffectivelyActive() ? 'badge-success' : 'badge-danger' }}">
                {{ $menu->isEffectivelyActive() ? 'Active' : 'Inactive' }}
            </span>

            <div class="flex items-center border-l pl-4 border-slate-100 space-x-1">
                <button type="button" class="btn-icon edit-btn p-2!" data-id="{{ $menu->id }}"
                    data-name="{{ $menu->name }}" data-parent="{{ $menu->parent_id }}"
                    data-active="{{ $menu->is_active ? '1' : '0' }}"
                    data-multi="{{ $menu->is_multifunctional ? '1' : '0' }}">
                    <i class="fas fa-pencil text-xs"></i>
                </button>

                <form action="{{ route('admin.menus.delete', $menu) }}" method="POST"
                    onsubmit="return confirm('Delete this menu?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-danger p-2!"><i class="fas fa-trash-can text-xs"></i></button>
                </form>
            </div>
        </div>
    </div>

    <ul
        class="nested-sortable-list ml-12 p-4 bg-slate-50 border border-dashed border-slate-200 rounded-3xl min-h-5 space-y-3">
        @foreach($menu->children as $child)
            @include('admin.menus.partials.menu-item', ['menu' => $child])
        @endforeach
    </ul>
</li>
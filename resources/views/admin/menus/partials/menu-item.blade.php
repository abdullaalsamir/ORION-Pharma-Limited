<li data-id="{{ $menu->id }}" data-parent="{{ $menu->parent_id ?? 'root' }}">
    <div class="menu-card">
        <div class="menu-left">
            <button type="button" class="drag-handle" draggable="true" aria-label="Move menu">
                <i class="fas fa-arrows-up-down-left-right"></i>
            </button>

            @if($menu->children->count())
                <button type="button" class="collapse-toggle" data-target="#children-{{ $menu->id }}">
                    <i class="fas fa-chevron-right"></i>
                </button>
            @else
                <span style="width:24px;display:inline-block;"></span>
            @endif

            <div>
                <div class="menu-title">{{ $menu->name }}</div>
                <div class="small-note">
                    @if($menu->parent) Parent: {{ $menu->parent->name }} @endif
                </div>
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:10px;">
            <div>
                @if($menu->isEffectivelyActive())
                    <span class="menu-badge">Active</span>
                @else
                    <span class="menu-badge inactive">Inactive</span>
                @endif
            </div>

            <div class="menu-actions">
                <button type="button" class="icon-btn edit-btn" data-id="{{ $menu->id }}" data-name="{{ $menu->name }}"
                    data-parent="{{ $menu->parent_id }}" data-active="{{ $menu->is_active ? '1' : '0' }}">
                    <i class="fas fa-pen"></i>
                </button>

                <form action="{{ route('admin.menus.delete', $menu) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="icon-btn" onclick="return confirm('Delete this menu?')">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if($menu->children->count())
        <div id="children-{{ $menu->id }}" class="nested">
            <ul style="list-style:none;padding-left:0;margin:0;">
                @foreach($menu->children as $child)
                    @include('admin.menus.partials.menu-item', ['menu' => $child])
                @endforeach
            </ul>
        </div>
    @endif
</li>
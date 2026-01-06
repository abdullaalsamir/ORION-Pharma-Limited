<li>
    <strong>{{ $menu->name }}</strong>
    @if(!$menu->is_active) <em>(Inactive)</em> @endif

    <span class="menu-actions">

        <!-- EDIT BUTTON (NOT inside form) -->
        <button 
            type="button"
            class="edit-btn"
            data-id="{{ $menu->id }}"
            data-name="{{ $menu->name }}"
            data-parent="{{ $menu->parent_id }}"
            data-active="{{ $menu->is_active }}"
        >
            Edit
        </button>

        <!-- DELETE FORM -->
        <form action="{{ route('admin.menus.delete', $menu) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" onclick="return confirm('Delete this menu?')">
                Delete
            </button>
        </form>

    </span>

    @if($menu->children->count())
        <ul>
            @foreach($menu->children as $child)
                @include('admin.menus.partials.menu-item', ['menu' => $child])
            @endforeach
        </ul>
    @endif
</li>

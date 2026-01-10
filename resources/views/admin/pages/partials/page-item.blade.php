@if($menu->slug === 'home') @php return; @endphp @endif
<li data-id="{{ $menu->id }}">
    <div class="menu-card">
        <div class="menu-left">
            @if($menu->children->count())
                <button type="button" class="collapse-toggle" data-target="#children-{{ $menu->id }}">
                    <i class="fas fa-chevron-right"></i>
                </button>
            @else
                <span style="width:24px;"></span>
            @endif

            <div>
                <div class="menu-title">{{ $menu->name }}</div>
            </div>
        </div>

        <div class="menu-actions">
            @if($menu->is_multifunctional)
                <a href="{{ url('admin/' . $menu->slug) }}" class="icon-btn">
                    <i class="fas fa-external-link-alt"></i>
                </a>
            @else
                <button class="icon-btn edit-page" data-id="{{ $menu->id }}" data-name="{{ $menu->name }}"
                    data-content="{{ e($menu->content) }}">
                    <i class="fas fa-pen"></i>
                </button>
            @endif
        </div>
    </div>

    @if($menu->children->count())
        <div id="children-{{ $menu->id }}" class="nested">
            <ul>
                @foreach($menu->children as $child)
                    @include('admin.pages.partials.page-item', ['menu' => $child])
                @endforeach
            </ul>
        </div>
    @endif
</li>
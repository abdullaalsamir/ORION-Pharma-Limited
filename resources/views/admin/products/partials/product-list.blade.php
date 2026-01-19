<div class="image-area-header">
    <div style="display:flex; justify-content: space-between; align-items: center;">
        <div>
            <h3 style="margin:0">{{ $generic->name }}</h3>
            <small style="color:#64748b">{{ $products->count() }} Products</small>
        </div>
        <button onclick="openAddProduct()"
            style="background:#1e7a43; color:#fff; border:none; padding:10px 20px; border-radius:8px; cursor:pointer;">
            <i class="fas fa-plus"></i> Add Product
        </button>
    </div>
</div>

<div class="image-area-body" style="padding:20px; overflow-y:auto;">
    <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap:20px;">
        @foreach($products as $p)
            @php $isLocked = !$generic->is_active; @endphp
            <div class="menu-card"
                style="flex-direction:column; align-items:stretch; opacity: {{ ($isLocked || !$p->is_active) ? '0.6' : '1' }}">
                <div style="aspect-ratio:16/9; overflow:hidden; border-radius:6px; margin-bottom:10px; background:#f4f6f8">
                    <img src="{{ asset('storage/' . $p->image_path) }}" style="width:100%; height:100%; object-fit:cover">
                </div>
                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                    <div>
                        <div style="font-weight:700">{{ $p->trade_name }}</div>
                        <div style="font-size:11px; color:#666">{{ $p->preparation }}</div>
                    </div>
                    <div style="display:flex; gap:5px;">
                        <button class="icon-btn" {{ $isLocked ? 'disabled title="Generic is Inactive"' : '' }}
                            onclick="openEditProduct({{ json_encode($p) }})"
                            style="{{ $isLocked ? 'opacity: 0.5; cursor: not-allowed;' : '' }}">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button class="icon-btn" style="color:red" onclick="deleteProduct({{ $p->id }})"><i
                                class="fas fa-trash"></i></button>
                    </div>
                </div>
                <div style="margin-top:10px; display:flex; gap:10px;">
                    @if($isLocked)
                        <span class="menu-badge inactive" style="font-size:10px;">Locked by Generic</span>
                    @elseif(!$p->is_active)
                        <span class="menu-badge inactive" style="font-size:10px;">Inactive</span>
                    @else
                        <span class="menu-badge" style="font-size:10px;">Active</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
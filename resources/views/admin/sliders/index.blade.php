@extends('admin.layouts.app')

@section('title', 'Swiper Slider')

@section('content')
    <style>
        .slider-row {
            display: flex;
            align-items: center;
            gap: 15px;
            background: #fff;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #eef1f6;
            margin-bottom: 10px;
        }

        .slider-preview {
            width: 200px;
            aspect-ratio: 10/4;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .ratio-10-4 {
            width: 100%;
            aspect-ratio: 10/4;
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .ratio-10-4 img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .drag-handle {
            cursor: move;
            color: #ccc;
            padding: 10px;
        }
    </style>

    <div class="card">
        <div class="card-header" style="display:flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 style="margin:0">Swiper Slider Management</h3>
                <small style="color:#666">Manage home page slider images and content (10:4 Ratio)</small>
            </div>
            <button onclick="openAddModal()"
                style="background:#1e7a43; color:#fff; border:none; padding:10px 20px; border-radius:8px; cursor:pointer;">
                <i class="fas fa-plus"></i> Add Slider
            </button>
        </div>

        <div class="card-body">
            <div id="slider-list">
                @foreach($sliders as $slider)
                    <div class="slider-row" data-id="{{ $slider->id }}"
                        style="display: flex; align-items: center; gap: 15px; background: #fff; padding: 15px; border-radius: 8px; border: 1px solid #eef1f6; margin-bottom: 10px;">

                        <div class="drag-handle" style="cursor: move; color: #ccc; padding: 10px 5px;"><i
                                class="fas fa-bars"></i></div>

                        <img src="{{ asset('storage/' . $slider->image_path) }}"
                            style="width: 200px; aspect-ratio: 10/4; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; flex-shrink: 0;">

                        <div
                            style="flex: 1; align-self: flex-start; display: flex; flex-direction: column; gap: 2px; padding-top: 2px; min-width: 0;">
                            <div style="font-weight:700; font-size: 16px; color: #1e293b; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                title="{{ $slider->header_1 }}">
                                {{ $slider->header_1 }}
                            </div>
                            <div style="color:#0054a6; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                title="{{ $slider->header_2 }}">
                                {{ $slider->header_2 }}
                            </div>
                            <div style="font-size:13px; color:#64748b; margin-top: 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                title="{{ $slider->description }}">
                                {{ $slider->description }}
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 10px; flex-shrink: 0;">
                            @php
                                $cleanUrl = ltrim($slider->link_url, '/');
                                $linkedMenu = $allMenus->first(function ($m) use ($cleanUrl) {
                                    return $m->full_slug === $cleanUrl;
                                });
                            @endphp

                            @if($linkedMenu)
                                <span class="link-badge" title="URL: {{ $slider->link_url }}"
                                    style="display: flex; align-items: center; font-size: 12px; padding: 4px 12px; border-radius: 999px; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; white-space: nowrap;">
                                    <i class="fas fa-link" style="font-size: 10px; margin-right: 7px; color: #0054a6;"></i>
                                    {{ $linkedMenu->name }}
                                </span>
                            @endif

                            @if($slider->is_active)
                                <span class="menu-badge">Active</span>
                            @else
                                <span class="menu-badge inactive">Inactive</span>
                            @endif
                        </div>

                        <div class="menu-actions" style="flex-shrink: 0; display: flex; align-items: center; gap: 5px;">
                            <button class="icon-btn" onclick="openEditModal({{ json_encode($slider) }})">
                                <i class="fas fa-pen"></i>
                            </button>
                            <form action="{{ route('admin.sliders.delete', $slider) }}" method="POST" style="display:inline"
                                onsubmit="return confirm('Delete slider?')">
                                @csrf @method('DELETE')
                                <button class="icon-btn" style="color:red"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div id="addModal" class="modal-overlay">
        <div class="modal-content" style="width: 700px;">
            <button onclick="closeModal('addModal')" class="modal-close"><i class="fas fa-times"></i></button>
            <h3>Add New Slider</h3>
            <form action="{{ route('admin.sliders.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="ratio-10-4" id="addPreview"><span style="color:#94a3b8">10:4 Ratio Preview</span></div>
                <input type="file" name="image" required onchange="preview(this, 'addPreview')"
                    style="margin: 10px 0; width: 100%;">

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px; margin-bottom:10px;">
                    <div style="position: relative;">
                        <input type="text" name="header_1" placeholder="Header Line 1" maxlength="22" required
                            oninput="updateCount(this, 'addC1', 22)"
                            style="width:100%; padding:10px 45px 10px 10px; border:1px solid #ddd; border-radius:6px;">
                        <span id="addC1"
                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); font-size: 11px; color: #999; pointer-events: none;">0/22</span>
                    </div>

                    <div style="position: relative;">
                        <input type="text" name="header_2" placeholder="Header Line 2 (Highlight)" maxlength="22" required
                            oninput="updateCount(this, 'addC2', 22)"
                            style="width:100%; padding:10px 45px 10px 10px; border:1px solid #ddd; border-radius:6px;">
                        <span id="addC2"
                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); font-size: 11px; color: #999; pointer-events: none;">0/22</span>
                    </div>
                </div>

                <div style="position: relative; margin-bottom:10px;">
                    <textarea name="description" placeholder="Description" maxlength="150" required
                        oninput="updateCount(this, 'addCD', 150)"
                        style="width:100%; height:50px; line-height:1.2; padding:8px 10px 18px 10px; border:1px solid #ddd; border-radius:6px; resize: none; overflow:hidden;"></textarea>
                    <span id="addCD"
                        style="position: absolute; right: 10px; bottom: 10px; font-size: 11px; color: #999; pointer-events: none;">0/150</span>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 2fr; gap:15px; margin-bottom:15px;">
                    <div style="position: relative;">
                        <input type="text" name="button_text" value="Explore More" placeholder="Button Name" maxlength="15"
                            required oninput="updateCount(this, 'addCBT', 15)"
                            style="width:100%; padding:9px 45px 9px 10px; border:1px solid #e6e9ee; border-radius:6px;">
                        <span id="addCBT"
                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); font-size: 11px; color: #999; pointer-events: none;">12/15</span>
                    </div>

                    <select name="link_url" id="addLink" required
                        style="padding:9px 12px; border:1px solid #e6e9ee; border-radius:6px; width:100%;">
                        <option value="">⁝⁝⁝ Select Link ⁝⁝⁝</option>
                        @foreach($menus as $m)
                            @php $mIsCat = $m->children->count() > 0; @endphp
                            <option value="/{{ $m->full_slug }}" {{ $mIsCat ? 'disabled' : '' }}
                                style="{{ $mIsCat ? 'color: darkred; font-weight: bold;' : '' }}">{{ $m->name }}</option>
                            @foreach($m->children as $c)
                                @php $cIsCat = $c->children->count() > 0; @endphp
                                <option value="/{{ $c->full_slug }}" {{ $cIsCat ? 'disabled' : '' }}
                                    style="{{ $cIsCat ? 'color: darkred;' : 'color: gray;' }}">— {{ $c->name }}</option>
                                @foreach($c->children as $sc)
                                    <option value="/{{ $sc->full_slug }}" style="color: gray;">&nbsp;&nbsp;&nbsp;— {{ $sc->name }}
                                    </option>
                                @endforeach
                            @endforeach
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                    style="width:100%; background:#0a3d62; color:#fff; border:none; padding:12px; border-radius:8px; cursor:pointer;">Upload
                    Slider</button>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal-overlay">
        <div class="modal-content" style="width: 700px;">
            <button onclick="closeModal('editModal')" class="modal-close"><i class="fas fa-times"></i></button>
            <h3>Edit Slider</h3>
            <form id="editForm">
                <div class="ratio-10-4" id="editPreview"></div>
                <input type="file" name="image" onchange="preview(this, 'editPreview')"
                    style="margin: 10px 0; width: 100%;">

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px; margin-bottom:10px;">
                    <div style="position: relative;">
                        <input type="text" name="header_1" placeholder="Header Line 1" id="editH1" maxlength="22" required
                            oninput="updateCount(this, 'editC1', 22)"
                            style="width:100%; padding:10px 45px 10px 10px; border:1px solid #ddd; border-radius:6px;">
                        <span id="editC1"
                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); font-size: 11px; color: #999; pointer-events: none;">0/22</span>
                    </div>

                    <div style="position: relative;">
                        <input type="text" name="header_2" placeholder="Header Line 2 (Highlight)" id="editH2"
                            maxlength="22" required oninput="updateCount(this, 'editC2', 22)"
                            style="width:100%; padding:10px 45px 10px 10px; border:1px solid #ddd; border-radius:6px;">
                        <span id="editC2"
                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); font-size: 11px; color: #999; pointer-events: none;">0/22</span>
                    </div>
                </div>

                <div style="position: relative; margin-bottom:10px;">
                    <textarea name="description" id="editDesc" placeholder="Description" maxlength="150" required
                        oninput="updateCount(this, 'editCD', 150)"
                        style="width:100%; height:50px; line-height:1.2; padding:8px 10px 18px 10px; border:1px solid #ddd; border-radius:6px; resize: none; overflow:hidden;"></textarea>
                    <span id="editCD"
                        style="position: absolute; right: 10px; bottom: 10px; font-size: 11px; color: #999; pointer-events: none;">0/150</span>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 2fr; gap:15px; margin-bottom:15px;">
                    <div style="position: relative;">
                        <input type="text" name="button_text" id="editBT" placeholder="Button Name" maxlength="15" required
                            oninput="updateCount(this, 'editCBT', 15)"
                            style="width:100%; padding:9px 45px 9px 10px; border:1px solid #e6e9ee; border-radius:6px;">
                        <span id="editCBT"
                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); font-size: 11px; color: #999; pointer-events: none;">0/15</span>
                    </div>

                    <select name="link_url" id="addLink" required
                        style="padding:9px 12px; border:1px solid #e6e9ee; border-radius:6px; width:100%;">
                        <option value="">⁝⁝⁝ Select Link ⁝⁝⁝</option>
                        @foreach($menus as $m)
                            @php $mIsCat = $m->children->count() > 0; @endphp
                            <option value="/{{ $m->full_slug }}" {{ $mIsCat ? 'disabled' : '' }}
                                style="{{ $mIsCat ? 'color: darkred; font-weight: bold;' : '' }}">{{ $m->name }}</option>
                            @foreach($m->children as $c)
                                @php $cIsCat = $c->children->count() > 0; @endphp
                                <option value="/{{ $c->full_slug }}" {{ $cIsCat ? 'disabled' : '' }}
                                    style="{{ $cIsCat ? 'color: darkred;' : 'color: gray;' }}">— {{ $c->name }}</option>
                                @foreach($c->children as $sc)
                                    <option value="/{{ $sc->full_slug }}" style="color: gray;">&nbsp;&nbsp;&nbsp;— {{ $sc->name }}
                                    </option>
                                @endforeach
                            @endforeach
                        @endforeach
                    </select>
                </div>

                <label style="display:flex; align-items:center; gap:10px; margin-bottom:15px; cursor:pointer;">
                    <input type="checkbox" name="is_active" id="editActive">
                    <span style="font-weight:600; font-size:14px;">Active Status</span>
                </label>

                <button type="submit"
                    style="width:100%; background:#0a3d62; color:#fff; border:none; padding:12px; border-radius:8px; cursor:pointer;">Update
                    Slider</button>
            </form>
        </div>
    </div>

    @include('admin.partials.menu-tree-css')

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
        <script>
            function preview(input, id) {
                if (input.files && input.files[0]) {
                    let reader = new FileReader();
                    reader.onload = e => document.getElementById(id).innerHTML = `<img src="${e.target.result}">`;
                    reader.readAsDataURL(input.files[0]);
                }
            }

            function openAddModal() {
                document.getElementById('addModal').style.display = 'flex';
                setTimeout(() => document.getElementById('addModal').classList.add('active'), 10);
            }

            let currentEditId = null;
            function openEditModal(slider) {
                currentEditId = slider.id;

                const h1 = document.getElementById('editH1');
                const h2 = document.getElementById('editH2');
                const desc = document.getElementById('editDesc');
                const bt = document.getElementById('editBT');
                const link = document.getElementById('editLink');
                const active = document.getElementById('editActive');

                h1.value = slider.header_1;
                h2.value = slider.header_2;
                desc.value = slider.description;
                bt.value = slider.button_text;

                if (link) link.value = slider.link_url;
                if (active) active.checked = (slider.is_active == 1);

                updateCount(h1, 'editC1', 22);
                updateCount(h2, 'editC2', 22);
                updateCount(desc, 'editCD', 150);
                updateCount(bt, 'editCBT', 15);

                document.getElementById('editPreview').innerHTML = `<img src="/storage/${slider.image_path}">`;

                const modal = document.getElementById('editModal');
                modal.style.display = 'flex';
                setTimeout(() => modal.classList.add('active'), 10);
            }

            function closeModal(id) {
                document.getElementById(id).classList.remove('active');
                setTimeout(() => document.getElementById(id).style.display = 'none', 300);
            }

            function updateCount(el, counterId, limit) {
                const len = el.value.length;
                const counter = document.getElementById(counterId);
                if (counter) {
                    counter.innerText = `${len}/${limit}`;
                    counter.style.color = (len >= limit) ? '#e11d48' : '#999';
                }
            }

            document.getElementById('editForm').onsubmit = function (e) {
                e.preventDefault();
                let formData = new FormData(this);
                formData.append('_method', 'PUT');
                formData.append('is_active', document.getElementById('editActive').checked ? 1 : 0);

                fetch(`/admin/sliders/${currentEditId}`, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                }).then(() => window.location.reload());
            }

            new Sortable(document.getElementById('slider-list'), {
                handle: '.drag-handle',
                animation: 150,
                onEnd: function () {
                    let orders = [];
                    document.querySelectorAll('.slider-row').forEach((el, index) => {
                        orders.push({
                            id: el.dataset.id,
                            order: index + 1
                        });
                    });

                    fetch('{{ route("admin.sliders.update-order") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ orders })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                console.log('Order updated successfully');
                            }
                        });
                }
            });
        </script>
    @endpush
@endsection
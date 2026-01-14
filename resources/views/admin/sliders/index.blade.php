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
                    <div class="slider-row" data-id="{{ $slider->id }}">
                        <div class="drag-handle"><i class="fas fa-bars"></i></div>
                        <img src="{{ asset('storage/' . $slider->image_path) }}" class="slider-preview">
                        <div style="flex:1">
                            <div style="font-weight:600">{{ $slider->header_1 }}</div>
                            <div style="font-size:12px; color:#0054a6">{{ $slider->header_2 }}</div>
                        </div>
                        <div>
                            @if($slider->is_active)
                                <span class="menu-badge">Active</span>
                            @else
                                <span class="menu-badge inactive">Inactive</span>
                            @endif
                        </div>
                        <div class="menu-actions">
                            <button class="icon-btn" onclick="openEditModal({{ json_encode($slider) }})"><i
                                    class="fas fa-pen"></i></button>
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

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-bottom:10px;">
                    <input type="text" name="header_1" placeholder="Header Line 1" required
                        style="padding:10px; border:1px solid #ddd; border-radius:6px;">
                    <input type="text" name="header_2" placeholder="Header Line 2 (Highlight)" required
                        style="padding:10px; border:1px solid #ddd; border-radius:6px;">
                </div>
                <textarea name="description" placeholder="Paragraph description" required
                    style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; margin-bottom:10px;"></textarea>

                <select name="link_url" required
                    style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; margin-bottom:15px;">
                    <option value="">Select Hyperlink Menu</option>
                    @foreach($links as $link)
                        <option value="/{{ $link->full_slug }}">{{ $link->name }} ({{ $link->full_slug }})</option>
                    @endforeach
                </select>

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

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-bottom:10px;">
                    <input type="text" name="header_1" id="editH1" required
                        style="padding:10px; border:1px solid #ddd; border-radius:6px;">
                    <input type="text" name="header_2" id="editH2" required
                        style="padding:10px; border:1px solid #ddd; border-radius:6px;">
                </div>
                <textarea name="description" id="editDesc" required
                    style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; margin-bottom:10px;"></textarea>

                <select name="link_url" id="editLink" required
                    style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; margin-bottom:15px;">
                    @foreach($links as $link)
                        <option value="/{{ $link->full_slug }}">{{ $link->name }}</option>
                    @endforeach
                </select>

                <label style="display:flex; align-items:center; gap:10px; margin-bottom:15px;">
                    <input type="checkbox" name="is_active" id="editActive"> Active
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
                document.getElementById('editH1').value = slider.header_1;
                document.getElementById('editH2').value = slider.header_2;
                document.getElementById('editDesc').value = slider.description;
                document.getElementById('editLink').value = slider.link_url;
                document.getElementById('editActive').checked = slider.is_active;
                document.getElementById('editPreview').innerHTML = `<img src="/storage/${slider.image_path}">`;

                document.getElementById('editModal').style.display = 'flex';
                setTimeout(() => document.getElementById('editModal').classList.add('active'), 10);
            }

            function closeModal(id) {
                document.getElementById(id).classList.remove('active');
                setTimeout(() => document.getElementById(id).style.display = 'none', 300);
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
                        orders.push({ id: el.dataset.id, order: index });
                    });
                    fetch('{{ route("admin.sliders.update-order") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ orders })
                    });
                }
            });
        </script>
    @endpush
@endsection
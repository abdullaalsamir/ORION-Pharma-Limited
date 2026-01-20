@extends('admin.layouts.app')
@section('title', 'Board of Directors Management')

@section('content')
    <style>
        .news-row {
            display: flex;
            align-items: center;
            gap: 15px;
            background: #fff;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #eef1f6;
            margin-bottom: 10px;
        }

        .drag-handle {
            cursor: move;
            color: #ccc;
            padding: 10px;
        }

        .news-details {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .news-title {
            font-weight: 700;
            font-size: 16px;
            color: #1e293b;
        }

        .news-meta {
            font-size: 12px;
            color: #0054a6;
            font-weight: 600;
        }

        .news-desc {
            font-size: 13px;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .ratio-3-4 {
            width: 200px;
            margin: 0 auto;
            aspect-ratio: 3/4;
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ratio-3-4 img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>

    <div class="card">
        <div class="card-header" style="display:flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 style="margin:0">Board of Directors</h3>
                <small style="color:#666">Manage profiles and reorder using drag-and-drop</small>
            </div>
            <button onclick="openAddModal()"
                style="background:#1e7a43; color:#fff; border:none; padding:10px 20px; border-radius:8px; cursor:pointer;">
                <i class="fas fa-plus"></i> Add Director
            </button>
        </div>

        <div class="card-body">
            <div id="sortable-list">
                @forelse($items as $item)
                    <div class="news-row" data-id="{{ $item->id }}">
                        <div class="drag-handle"><i class="fas fa-bars"></i></div>

                        <img src="{{ url($menu->full_slug . '/' . basename($item->image_path)) }}"
                            style="width: 100px; aspect-ratio:3:4; object-fit:cover; border-radius:4px; border:1px solid #ddd;">

                        <div class="news-details">
                            <div class="news-title">{{ $item->name }}</div>
                            <div class="news-meta">{{ $item->designation }}</div>
                            <div class="news-desc">{{ strip_tags($item->description) }}</div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 15px; flex-shrink: 0;">
                            <span
                                class="menu-badge {{ $item->is_active ? '' : 'inactive' }}">{{ $item->is_active ? 'Active' : 'Inactive' }}</span>
                            <div class="menu-actions">
                                <button class="icon-btn" onclick="openEditModal({{ json_encode($item) }})"><i
                                        class="fas fa-pen"></i></button>
                                <form action="{{ route('admin.directors.delete', $item) }}" method="POST" style="display:inline"
                                    onsubmit="return confirm('Delete this profile?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="icon-btn" style="color:red"><i
                                            class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="text-align:center; padding:50px; color:#ccc;">
                        <i class="fas fa-user-tie" style="font-size:40px; margin-bottom:10px"></i>
                        <p>No directors found.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div id="addModal" class="modal-overlay">
        <div class="modal-content" style="width: 700px;">
            <button onclick="closeModal('addModal')" class="modal-close"><i class="fas fa-times"></i></button>
            <h3>Add New Director</h3>
            <form action="{{ route('admin.directors.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="ratio-3-4" id="addPreview"><span style="color:#94a3b8">16:9 Photo Preview</span></div>
                <input type="file" name="image" required onchange="preview(this, 'addPreview')"
                    style="margin: 10px 0; width: 100%;">

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px; margin-bottom:10px;">
                    <input type="text" name="name" placeholder="Director Name" required
                        style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;">
                    <input type="text" name="designation" placeholder="Designation (e.g. Managing Director)" required
                        style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;">
                </div>

                <div style="position: relative; margin-bottom:15px;">
                    <textarea name="description" placeholder="Biography/Profile Details..." required
                        style="width:100%; height:150px; padding:10px; border:1px solid #ddd; border-radius:6px; resize: none;"></textarea>
                </div>

                <button type="submit"
                    style="width:100%; background:#0a3d62; color:#fff; border:none; padding:12px; border-radius:8px; cursor:pointer;">Save
                    Director</button>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal-overlay">
        <div class="modal-content" style="width: 700px;">
            <button onclick="closeModal('editModal')" class="modal-close"><i class="fas fa-times"></i></button>
            <h3>Edit Director Profile</h3>
            <form id="editForm">
                <div class="ratio-3-4" id="editPreview"></div>
                <input type="file" name="image" onchange="preview(this, 'editPreview')"
                    style="margin: 10px 0; width: 100%;">

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px; margin-bottom:10px;">
                    <input type="text" id="editName" name="name" required
                        style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;">
                    <input type="text" id="editDesignation" name="designation" required
                        style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;">
                </div>

                <div style="position: relative; margin-bottom:10px;">
                    <textarea id="editDesc" name="description" required
                        style="width:100%; height:150px; padding:10px; border:1px solid #ddd; border-radius:6px; resize: none;"></textarea>
                </div>

                <label style="display:flex; align-items:center; gap:10px; margin-bottom:15px; cursor:pointer;">
                    <input type="checkbox" name="is_active" id="editActive">
                    <span style="font-weight:600; font-size:14px;">Active Status</span>
                </label>

                <button type="submit"
                    style="width:100%; background:#0a3d62; color:#fff; border:none; padding:12px; border-radius:8px; cursor:pointer;">Update
                    Profile</button>
            </form>
        </div>
    </div>

    @include('admin.partials.css')

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
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
            function openEditModal(item) {
                currentEditId = item.id;
                document.getElementById('editName').value = item.name;
                document.getElementById('editDesignation').value = item.designation;
                document.getElementById('editDesc').value = item.description;
                document.getElementById('editActive').checked = (item.is_active == 1);

                const filename = item.image_path.split('/').pop();
                document.getElementById('editPreview').innerHTML = `<img src="/{{ $menu->full_slug }}/${filename}">`;

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

                fetch(`/admin/director-actions/${currentEditId}`, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                }).then(res => res.json()).then(data => {
                    if (data.success) window.location.reload();
                    else alert('Error updating profile');
                });
            }

            new Sortable(document.getElementById('sortable-list'), {
                handle: '.drag-handle',
                animation: 150,
                onEnd: function () {
                    let orders = [];
                    document.querySelectorAll('.news-row').forEach((row, index) => {
                        orders.push({ id: row.dataset.id, order: index + 1 });
                    });
                    fetch('{{ route("admin.directors.update-order") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ orders })
                    });
                }
            });
        </script>
    @endpush
@endsection
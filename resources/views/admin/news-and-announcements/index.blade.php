@extends('admin.layouts.app')

@section('title', 'News List Management')

@section('content')
    <style>
        .date-group {
            margin-bottom: 30px;
            border-left: 4px solid #0a3d62;
            padding-left: 15px;
        }

        .date-header {
            font-weight: 700;
            color: #0a3d62;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
        }

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

        .news-preview-img {
            width: 200px;
            aspect-ratio: 16/9;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
            flex-shrink: 0;
        }

        .ratio-16-9 {
            width: 100%;
            aspect-ratio: 16/9;
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .ratio-16-9 img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .news-desc {
            font-size: 13px;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .news-meta {
            font-size: 12px;
            color: #0054a6;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
        }
    </style>

    <div class="card">
        <div class="card-header" style="display:flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 style="margin:0">News Management</h3>
                <small style="color:#666">Manage News items (16:9 Ratio)</small>
            </div>
            <button onclick="openAddModal()"
                style="background:#1e7a43; color:#fff; border:none; padding:10px 20px; border-radius:8px; cursor:pointer;">
                <i class="fas fa-plus"></i> Add News Item
            </button>
        </div>

        <div class="card-body">
            @forelse($groupedNews as $date => $items)
                <div class="date-group">
                    <div class="date-header">
                        <i class="far fa-calendar-alt"></i> {{ date('F d, Y', strtotime($date)) }}
                    </div>

                    <div class="sortable-list" data-date="{{ $date }}">
                        @foreach($items as $item)
                            <div class="news-row" data-id="{{ $item->id }}">
                                <div class="drag-handle" style="{{ $items->count() > 1 ? '' : 'visibility:hidden' }}">
                                    <i class="fas fa-bars"></i>
                                </div>

                                <img src="{{ url($menu->full_slug . '/' . basename($item->image_path)) }}" class="news-preview-img">

                                <div class="news-details">
                                    <div class="news-title" title="{{ $item->title }}">{{ $item->title }}</div>
                                    <div class="news-desc" title="{{ $item->description }}">{{ $item->description }}</div>
                                    <div class="news-meta">
                                        <i class="far fa-clock"></i> {{ $item->news_date->format('M d, Y') }}
                                    </div>
                                </div>

                                <div style="display: flex; align-items: center; gap: 15px; flex-shrink: 0;">
                                    @if($item->is_active)
                                        <span class="menu-badge">Active</span>
                                    @else
                                        <span class="menu-badge inactive">Inactive</span>
                                    @endif

                                    <div class="menu-actions">
                                        <button class="icon-btn" onclick="openEditModal({{ json_encode($item) }})">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        <form action="{{ route('admin.news.delete', $item) }}" method="POST" style="display:inline"
                                            onsubmit="return confirm('Delete this News?')">
                                            @csrf @method('DELETE')
                                            <button class="icon-btn" style="color:red"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div style="text-align:center; padding:50px; color:#ccc;">
                    <i class="fas fa-folder-open" style="font-size:40px; margin-bottom:10px"></i>
                    <p>No News items found.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Add Modal -->
    <div id="addModal" class="modal-overlay">
        <div class="modal-content" style="width: 700px;">
            <button onclick="closeModal('addModal')" class="modal-close"><i class="fas fa-times"></i></button>
            <h3>Add New News</h3>
            <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="ratio-16-9" id="addPreview"><span style="color:#94a3b8">16:9 Ratio Preview</span></div>
                <input type="file" name="image" required onchange="preview(this, 'addPreview')"
                    style="margin: 10px 0; width: 100%;">

                <div style="display:grid; grid-template-columns: 2fr 1fr; gap:15px; margin-bottom:10px;">
                    <div style="position: relative;">
                        <input type="text" name="title" placeholder="News Title" maxlength="100" required
                            oninput="updateCount(this, 'addC1', 100)"
                            style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;">
                        <span id="addC1"
                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); font-size: 11px; color: #999;">0/100</span>
                    </div>
                    <input type="date" name="news_date" required
                        style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;">
                </div>

                <div style="position: relative; margin-bottom:15px;">
                    <textarea name="description" placeholder="Short Description..." maxlength="500" required
                        oninput="updateCount(this, 'addCD', 500)"
                        style="width:100%; height:80px; padding:10px; border:1px solid #ddd; border-radius:6px; resize: none;"></textarea>
                    <span id="addCD"
                        style="position: absolute; right: 10px; bottom: 10px; font-size: 11px; color: #999;">0/500</span>
                </div>

                <button type="submit"
                    style="width:100%; background:#0a3d62; color:#fff; border:none; padding:12px; border-radius:8px; cursor:pointer;">Upload
                    News</button>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal-overlay">
        <div class="modal-content" style="width: 700px;">
            <button onclick="closeModal('editModal')" class="modal-close"><i class="fas fa-times"></i></button>
            <h3>Edit News Item</h3>
            <form id="editForm">
                <div class="ratio-16-9" id="editPreview"></div>
                <input type="file" name="image" onchange="preview(this, 'editPreview')"
                    style="margin: 10px 0; width: 100%;">

                <div style="display:grid; grid-template-columns: 2fr 1fr; gap:15px; margin-bottom:10px;">
                    <div style="position: relative;">
                        <input type="text" id="editTitle" name="title" maxlength="100" required
                            oninput="updateCount(this, 'editC1', 100)"
                            style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;">
                        <span id="editC1"
                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); font-size: 11px; color: #999;">0/100</span>
                    </div>
                    <input type="date" id="editDate" name="news_date" required
                        style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;">
                </div>

                <div style="position: relative; margin-bottom:10px;">
                    <textarea id="editDesc" name="description" maxlength="500" required
                        oninput="updateCount(this, 'editCD', 500)"
                        style="width:100%; height:80px; padding:10px; border:1px solid #ddd; border-radius:6px; resize: none;"></textarea>
                    <span id="editCD"
                        style="position: absolute; right: 10px; bottom: 10px; font-size: 11px; color: #999;">0/500</span>
                </div>

                <label style="display:flex; align-items:center; gap:10px; margin-bottom:15px; cursor:pointer;">
                    <input type="checkbox" name="is_active" id="editActive">
                    <span style="font-weight:600; font-size:14px;">Active Status</span>
                </label>

                <button type="submit"
                    style="width:100%; background:#0a3d62; color:#fff; border:none; padding:12px; border-radius:8px; cursor:pointer;">Update
                    News</button>
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

            function updateCount(el, counterId, limit) {
                const len = el.value.length;
                document.getElementById(counterId).innerText = `${len}/${limit}`;
            }

            function openAddModal() {
                document.getElementById('addModal').style.display = 'flex';
                setTimeout(() => document.getElementById('addModal').classList.add('active'), 10);
            }

            let currentEditId = null;
            function openEditModal(item) {
                currentEditId = item.id;
                document.getElementById('editTitle').value = item.title;
                document.getElementById('editDesc').value = item.description;
                document.getElementById('editDate').value = item.news_date.split('T')[0];
                document.getElementById('editActive').checked = (item.is_active == 1);

                const filename = item.image_path.split('/').pop();
                const fullSlug = "{{ $menu->full_slug }}";
                document.getElementById('editPreview').innerHTML = `<img src="/${fullSlug}/${filename}">`;

                updateCount(document.getElementById('editTitle'), 'editC1', 100);
                updateCount(document.getElementById('editDesc'), 'editCD', 500);

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

                fetch(`/admin/news-and-announcements/${currentEditId}`, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                }).then(res => res.json()).then(data => {
                    if (data.success) window.location.reload();
                    else alert(data.error);
                });
            }

            document.querySelectorAll('.sortable-list').forEach(el => {
                new Sortable(el, {
                    handle: '.drag-handle',
                    animation: 150,
                    onEnd: function () {
                        let orders = [];
                        el.querySelectorAll('.news-row').forEach((row, index) => {
                            orders.push({ id: row.dataset.id, order: index + 1 });
                        });
                        fetch('{{ route("admin.news.update-order") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ orders })
                        });
                    }
                });
            });
        </script>
    @endpush
@endsection
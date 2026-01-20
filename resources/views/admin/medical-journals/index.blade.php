@extends('admin.layouts.app')
@section('title', 'Medical Journals')

@section('content')
    <div class="card" style="height: calc(100vh - 100px);">
        <div class="card-header" style="display:flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 style="margin:0">Medical Journals</h3>
                <small>Organize PDFs by year and drag to reorder</small>
            </div>
            <button onclick="openAddModal()"
                style="background:#1e7a43; color:#fff; border:none; padding:10px 20px; border-radius:8px; cursor:pointer;">
                <i class="fas fa-plus"></i> Add Journal
            </button>
        </div>

        <div class="card-body scrollable-content menu-tree-wrapper">
            @forelse($groupedJournals as $year => $journals)
                <div class="year-group" style="margin-bottom:30px; border-left:4px solid #0a3d62; padding-left:15px;">
                    <h4 style="color:#0a3d62; margin-bottom:15px;"><i class="fas fa-calendar-alt"></i> {{ $year }}</h4>
                    <div class="sortable-list" data-year="{{ $year }}">
                        @foreach($journals as $j)
                            <div class="menu-card" data-id="{{ $j->id }}" style="margin-bottom:8px;">
                                <div class="menu-left">
                                    <div class="drag-handle"><i class="fas fa-bars"></i></div>
                                    <i class="fas fa-file-pdf" style="color:#e11d48; font-size:20px; margin:0 10px;"></i>
                                    <div class="menu-title">{{ $j->title }}</div>
                                </div>
                                <div style="display:flex; align-items:center; gap:15px;">
                                    <span
                                        class="menu-badge {{ $j->is_active ? '' : 'inactive' }}">{{ $j->is_active ? 'Active' : 'Inactive' }}</span>

                                    <a href="{{ url($menu->full_slug . '/' . $j->year . '/' . $j->filename) }}" target="_blank"
                                        class="icon-btn" style="color:#0a3d62" title="Preview PDF">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <div class="menu-actions">
                                        <button class="icon-btn" onclick="openEditModal({{ json_encode($j) }})"><i
                                                class="fas fa-pen"></i></button>
                                        <form action="{{ route('admin.journals.delete', $j) }}" method="POST" style="display:inline"
                                            onsubmit="return confirm('Delete?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="icon-btn" style="color:red"><i
                                                    class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <p style="text-align:center; color:#ccc; padding:50px;">No journals found.</p>
            @endforelse
        </div>
    </div>

    <div id="addModal" class="modal-overlay">
        <div class="modal-content" style="width: 500px;">
            <button onclick="closeModal('addModal')" class="modal-close"><i class="fas fa-times"></i></button>
            <h3>Add New Journal</h3>
            <form action="{{ route('admin.journals.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label style="font-size:13px; color:#666">Select PDF</label>
                <input type="file" name="pdf" id="pdfInput" accept="application/pdf" required
                    style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; margin-bottom:15px;">

                <label style="font-size:13px; color:#666">Filename / Title</label>
                <input type="text" name="title" id="titleInput" placeholder="Enter title" required
                    style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; margin-bottom:15px;">

                <label style="font-size:13px; color:#666">Publication Year</label>
                <select name="year" required
                    style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; margin-bottom:20px;">
                    @for($y = date('Y'); $y >= 2000; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>

                <button type="submit"
                    style="width:100%; background:#0a3d62; color:#fff; border:none; padding:12px; border-radius:8px;">Upload
                    Journal</button>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal-overlay">
        <div class="modal-content" style="width: 500px;">
            <button onclick="closeModal('editModal')" class="modal-close"><i class="fas fa-times"></i></button>
            <h3>Edit Journal</h3>
            <form id="editForm">
                <label style="font-size:13px; color:#666">Replace PDF (Optional)</label>
                <input type="file" name="pdf" accept="application/pdf"
                    style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; margin-bottom:15px;">

                <input type="text" name="title" id="editTitle" required
                    style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; margin-bottom:15px;">

                <select name="year" id="editYear" required
                    style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; margin-bottom:15px;">
                    @for($y = date('Y'); $y >= 2000; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>

                <label style="display:flex; align-items:center; gap:10px; margin-bottom:20px; cursor:pointer;">
                    <div class="toggle-switch"><input type="checkbox" id="editActive"><span class="slider"></span></div>
                    <span style="font-weight:600;">Active Status</span>
                </label>

                <button type="submit"
                    style="width:100%; background:#0a3d62; color:#fff; border:none; padding:12px; border-radius:8px;">Update
                    Journal</button>
            </form>
        </div>
    </div>

    @include('admin.partials.css')
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
        <script>
            document.getElementById('pdfInput').onchange = function () {
                if (this.files[0]) {
                    let name = this.files[0].name.replace(/\.[^/.]+$/, "");
                    document.getElementById('titleInput').value = name;
                }
            };

            function openAddModal() {
                document.getElementById('addModal').style.display = 'flex';
                setTimeout(() => document.getElementById('addModal').classList.add('active'), 10);
            }

            let currentEditId = null;
            function openEditModal(j) {
                currentEditId = j.id;
                document.getElementById('editTitle').value = j.title;
                document.getElementById('editYear').value = j.year;
                document.getElementById('editActive').checked = j.is_active == 1;
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
                fetch(`/admin/journal-actions/${currentEditId}`, {
                    method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                }).then(() => window.location.reload());
            };

            document.querySelectorAll('.sortable-list').forEach(el => {
                new Sortable(el, {
                    handle: '.drag-handle', animation: 150,
                    onEnd: function () {
                        let orders = [];
                        el.querySelectorAll('.menu-card').forEach((row, index) => {
                            orders.push({ id: row.dataset.id, order: index + 1 });
                        });
                        fetch('{{ route("admin.journals.update-order") }}', {
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
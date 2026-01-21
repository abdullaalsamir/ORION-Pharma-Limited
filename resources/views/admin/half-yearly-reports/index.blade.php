@extends('admin.layouts.app')
@section('title', 'Half Yearly Reports')

@section('content')
    <div class="card" style="height: calc(100vh - 100px);">
        <div class="card-header" style="display:flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 style="margin:0">Half Yearly Reports</h3>
                <small style="color:#666">Manage and organize Half Yearly Reports documents (Sorted by Date)</small>
            </div>
            <button onclick="openAddModal()"
                style="background:#1e7a43; color:#fff; border:none; padding:10px 20px; border-radius:8px; cursor:pointer;">
                <i class="fas fa-plus"></i> Add Information
            </button>
        </div>

        <div class="card-body scrollable-content menu-tree-wrapper">
            <div id="item-list">
                @forelse($items as $item)
                    <div class="menu-card" data-id="{{ $item->id }}" style="margin-bottom:8px;">
                        <div class="menu-left" style="overflow: hidden;">
                            <i class="fas fa-file-pdf" style="color:#e11d48; font-size:20px; margin:0 15px 0 5px;"></i>
                            <div style="overflow: hidden; flex: 1;">
                                <div class="menu-title" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $item->title }}
                                </div>
                                @if($item->description)
                                    <div
                                        style="font-size: 11px; color: #888; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 500px;">
                                        {{ $item->description }}
                                    </div>
                                @endif
                                <small style="color:#bbb; font-size: 10px;">Published:
                                    {{ $item->publication_date->format('d/m/Y') }}</small>
                            </div>
                        </div>
                        <div style="display:flex; align-items:center; gap:15px; flex-shrink: 0;">
                            <span
                                class="menu-badge {{ $item->is_active ? '' : 'inactive' }}">{{ $item->is_active ? 'Active' : 'Inactive' }}</span>

                            <a href="{{ url($menu->full_slug . '/' . $item->filename) }}" target="_blank" class="icon-btn"
                                style="color:#0a3d62"><i class="fas fa-eye"></i></a>

                            <div class="menu-actions">
                                <button class="icon-btn" onclick="openEditModal({{ json_encode($item) }})"><i
                                        class="fas fa-pen"></i></button>
                                <form action="{{ route('admin.half-yearly-reports.delete', $item) }}" method="POST"
                                    style="display:inline" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="icon-btn" style="color:red"><i
                                            class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <p style="text-align:center; color:#ccc; padding:50px;">No records found.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div id="addModal" class="modal-overlay">
        <div class="modal-content" style="width: 550px;">
            <button onclick="closeModal('addModal')" class="modal-close"><i class="fas fa-times"></i></button>
            <h3>Add Half Yearly Reports</h3>
            <form action="{{ route('admin.half-yearly-reports.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label style="font-size:12px; color:#666">Select PDF</label>
                <input type="file" name="pdf" id="pdfInput" accept="application/pdf" required
                    style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px; margin-bottom:12px;">

                <label style="font-size:12px; color:#666">Title</label>
                <input type="text" name="title" id="titleInput" required
                    style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px; margin-bottom:12px;">

                <label style="font-size:12px; color:#666">Description (Optional)</label>
                <div style="position: relative;">
                    <textarea name="description" id="addDesc" maxlength="500" oninput="updateCharCount(this, 'addCounter')"
                        style="width:100%; height:100px; padding:8px; border:1px solid #ddd; border-radius:6px; margin-bottom:12px; resize:none;"></textarea>
                    <small id="addCounter" style="position: absolute; bottom: 18px; right: 10px; color: #999;">0/500</small>
                </div>

                <label style="font-size:12px; color:#666">Publication Date</label>
                <input type="date" name="publication_date" required value="{{ date('Y-m-d') }}"
                    style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px; margin-bottom:20px;">

                <button type="submit"
                    style="width:100%; background:#0a3d62; color:#fff; border:none; padding:12px; border-radius:8px; cursor:pointer;">Save
                    Information</button>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal-overlay">
        <div class="modal-content" style="width: 550px;">
            <button onclick="closeModal('editModal')" class="modal-close"><i class="fas fa-times"></i></button>
            <h3>Edit Information</h3>
            <form id="editForm">
                <label style="font-size:12px; color:#666">Replace PDF (Optional)</label>
                <input type="file" name="pdf" accept="application/pdf"
                    style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px; margin-bottom:12px;">

                <label style="font-size:12px; color:#666">Title</label>
                <input type="text" name="title" id="editTitle" required
                    style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px; margin-bottom:12px;">

                <label style="font-size:12px; color:#666">Description (Optional)</label>
                <div style="position: relative;">
                    <textarea name="description" id="editDesc" maxlength="500"
                        oninput="updateCharCount(this, 'editCounter')"
                        style="width:100%; height:100px; padding:8px; border:1px solid #ddd; border-radius:6px; margin-bottom:12px; resize:none;"></textarea>
                    <small id="editCounter"
                        style="position: absolute; bottom: 18px; right: 10px; color: #999;">0/500</small>
                </div>

                <label style="font-size:12px; color:#666">Publication Date</label>
                <input type="date" name="publication_date" id="editDate" required
                    style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px; margin-bottom:15px;">

                <label style="display:flex; align-items:center; gap:10px; margin-bottom:20px; cursor:pointer;">
                    <div class="toggle-switch"><input type="checkbox" id="editActive"><span class="slider"></span></div>
                    <span style="font-weight:600;">Active Status</span>
                </label>

                <button type="submit"
                    style="width:100%; background:#0a3d62; color:#fff; border:none; padding:12px; border-radius:8px; cursor:pointer;">Update
                    Information</button>
            </form>
        </div>
    </div>

    @include('admin.partials.css')
    @push('scripts')
        <script>
            function updateCharCount(textarea, counterId) {
                const count = textarea.value.length;
                const counter = document.getElementById(counterId);
                counter.innerText = `${count}/500`;
                counter.style.color = count >= 500 ? 'red' : '#999';
            }

            document.getElementById('pdfInput').onchange = function () {
                if (this.files[0]) document.getElementById('titleInput').value = this.files[0].name.replace(/\.[^/.]+$/, "");
            };

            function openAddModal() {
                const addDesc = document.getElementById('addDesc');
                addDesc.value = '';
                updateCharCount(addDesc, 'addCounter');
                document.getElementById('addModal').style.display = 'flex';
                setTimeout(() => document.getElementById('addModal').classList.add('active'), 10);
            }

            let currentEditId = null;
            function openEditModal(item) {
                currentEditId = item.id;
                document.getElementById('editTitle').value = item.title;

                const editDesc = document.getElementById('editDesc');
                editDesc.value = item.description || '';
                updateCharCount(editDesc, 'editCounter');

                document.getElementById('editDate').value = item.publication_date.split('T')[0];
                document.getElementById('editActive').checked = item.is_active == 1;

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
                fetch(`/admin/half-yearly-reports-actions/${currentEditId}`, {
                    method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                }).then(res => {
                    if (!res.ok) return res.json().then(err => { throw err; });
                    return res.json();
                }).then(() => window.location.reload())
                    .catch(err => {
                        alert('Error: ' + (err.message || 'Validation failed. Make sure description is not over 500 characters.'));
                    });
            };
        </script>
    @endpush
@endsection
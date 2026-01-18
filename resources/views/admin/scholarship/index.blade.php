@extends('admin.layouts.app')
@section('title', 'Scholarship Management')

@section('content')
    <style>
        .scholar-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            table-layout: fixed;
        }

        .scholar-table th,
        .scholar-table td {
            padding: 12px;
            border: 1px solid #edf2f7;
            text-align: left;
            vertical-align: middle;
        }

        .table-responsive {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            border: 1px solid #edf2f7;
            border-radius: 8px;
            margin-top: 15px;
        }

        .prefix-input-wrapper {
            display: flex;
            align-items: center;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 0 10px;
            margin-bottom: 8px;
        }

        .prefix-text {
            color: #94a3b8;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            user-select: none;
        }

        .prefix-input {
            border: none !important;
            background: transparent !important;
            padding: 8px 5px !important;
            width: 100%;
            outline: none;
            font-size: 14px;
        }

        .list-photo {
            width: 50px;
            height: 61px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .modal-photo-preview {
            width: 120px;
            height: 146px;
            border: 2px dashed #cbd5e0;
            border-radius: 6px;
            margin-bottom: 10px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-photo-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .action-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .drag-handle {
            cursor: move;
            color: #cbd5e0;
            font-size: 18px;
        }
    </style>

    <div class="card" style="height: calc(100vh - 100px);">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h3 style="margin:0">Scholarship Management</h3>
                <small>Manage and organize scholarship recipients</small>
            </div>
            <button onclick="openAddModal()" class="btn-add-person"
                style="background:#1e7a43; color:#fff; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:600;">
                <i class="fas fa-plus"></i> Add Person
            </button>
        </div>

        <div class="table-responsive menu-tree-wrapper">
            <table class="scholar-table">
                <thead>
                    <tr style="background: #f8fafc; position: sticky; top: 0; z-index: 10;">
                        <th width="45"></th>
                        <th width="280">Name, Session & Roll</th>
                        <th>Name of Medical College</th>
                        <th width="80">Photo</th>
                        <th width="200">Action</th>
                    </tr>
                </thead>
                <tbody id="sortable-list">
                    @foreach($items as $item)
                        <tr data-id="{{ $item->id }}" class="scholar-row">
                            <td align="center"><i class="fas fa-grip-vertical drag-handle"></i></td>
                            <td>
                                <div style="font-weight:700; color:#1e293b; line-height: 1.2; margin-bottom: 4px;">
                                    {{ $item->name }}
                                </div>

                                @if($item->session)
                                    <div style="font-size:12px; color:#64748b;">{{ $item->session }}</div>
                                @endif

                                @if($item->roll_no)
                                    <div style="font-size:12px; color:#64748b;">{{ $item->roll_no }}</div>
                                @endif
                            </td>
                            <td>
                                {{ $item->medical_college }}
                            </td>
                            <td align="center">
                                <img src="{{ asset('storage/' . $item->image_path) }}" class="list-photo">
                            </td>
                            <td>
                                <div class="action-cell">
                                    @if($item->is_active)
                                        <span class="menu-badge">Active</span>
                                    @else
                                        <span class="menu-badge inactive">Inactive</span>
                                    @endif

                                    <button onclick="openEditModal({{ json_encode($item) }})" class="icon-btn"
                                        style="color:#0a3d62"><i class="fas fa-pen-to-square"></i></button>

                                    <form action="{{ route('admin.scholarship.delete', $item) }}" method="POST"
                                        onsubmit="return confirm('Delete?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="icon-btn" style="color:red"><i
                                                class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="addModal" class="modal-overlay">
        <div class="modal-content" style="width: 500px;">
            <button onclick="closeModal('addModal')" class="modal-close"><i class="fas fa-times"></i></button>
            <h3 style="margin-bottom: 20px;">Add New Person</h3>
            <form action="{{ route('admin.scholarship.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-photo-preview" id="addPhotoPreview"><i class="fas fa-camera fa-2x"
                        style="color:#cbd5e0"></i></div>
                <input type="file" name="image" required onchange="previewImg(this, 'addPhotoPreview')"
                    style="margin-bottom:15px; width:100%;">

                <input type="text" name="name" placeholder="Full Name" required
                    style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:6px; margin-bottom:10px;">

                <div class="prefix-input-wrapper">
                    <span class="prefix-text">Session:</span>
                    <input type="text" name="session" class="prefix-input" placeholder="2023-24">
                </div>

                <div class="prefix-input-wrapper">
                    <span class="prefix-text">Roll No:</span>
                    <input type="text" name="roll_no" class="prefix-input" placeholder="12345">
                </div>

                <input type="text" name="medical_college" placeholder="Medical College Name" required
                    style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:6px; margin-bottom:20px;">

                <button type="submit"
                    style="width:100%; background:#0a3d62; color:#fff; border:none; padding:12px; border-radius:8px; cursor:pointer;">Save
                    Person</button>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal-overlay">
        <div class="modal-content" style="width: 500px;">
            <button onclick="closeModal('editModal')" class="modal-close"><i class="fas fa-times"></i></button>
            <h3 style="margin-bottom: 20px;">Edit Details</h3>
            <form id="editForm">
                <div class="modal-photo-preview" id="editPhotoPreview"></div>
                <input type="file" name="image" onchange="previewImg(this, 'editPhotoPreview')"
                    style="margin-bottom:15px; width:100%;">

                <input type="text" name="name" id="editName" required
                    style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:6px; margin-bottom:10px;">

                <div class="prefix-input-wrapper">
                    <span class="prefix-text">Session:</span>
                    <input type="text" name="session" id="editSession" class="prefix-input">
                </div>

                <div class="prefix-input-wrapper">
                    <span class="prefix-text">Roll No:</span>
                    <input type="text" name="roll_no" id="editRoll" class="prefix-input">
                </div>

                <input type="text" name="medical_college" id="editCollege" required
                    style="width:100%; padding:10px; border:1px solid #e2e8f0; border-radius:6px; margin-bottom:15px;">

                <label style="display:flex; align-items:center; gap:10px; margin-bottom:20px; cursor:pointer;">
                    <div class="toggle-switch">
                        <input type="checkbox" name="is_active" id="editActive">
                        <span class="slider"></span>
                    </div>
                    <span style="font-weight:600;">Active Status</span>
                </label>

                <button type="submit"
                    style="width:100%; background:#0a3d62; color:#fff; border:none; padding:12px; border-radius:8px; cursor:pointer;">Update
                    Details</button>
            </form>
        </div>
    </div>

    @include('admin.partials.css')

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
        <script>
            new Sortable(document.getElementById('sortable-list'), {
                handle: '.drag-handle',
                animation: 150,
                onEnd: function () {
                    let orders = [];
                    document.querySelectorAll('.scholar-row').forEach((row, index) => {
                        orders.push({ id: row.dataset.id, order: index + 1 });
                    });
                    fetch('{{ route("admin.scholarship.update-order") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ orders })
                    });
                }
            });

            function previewImg(input, targetId) {
                if (input.files && input.files[0]) {
                    let reader = new FileReader();
                    reader.onload = e => document.getElementById(targetId).innerHTML = `<img src="${e.target.result}">`;
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
                document.getElementById('editCollege').value = item.medical_college;
                document.getElementById('editActive').checked = (item.is_active == 1);

                let sessionRaw = item.session ? item.session.replace('Session: ', '') : '';
                let rollRaw = item.roll_no ? item.roll_no.replace('Roll No: ', '') : '';

                document.getElementById('editSession').value = sessionRaw;
                document.getElementById('editRoll').value = rollRaw;

                document.getElementById('editPhotoPreview').innerHTML = `<img src="/storage/${item.image_path}">`;

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

                fetch(`/admin/scholarship-actions/${currentEditId}`, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                }).then(() => window.location.reload());
            }
        </script>
    @endpush
@endsection
@extends('admin.layouts.app')

@section('title', 'Images')

@section('content')
    <style>
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 28px;
            vertical-align: middle;
        }

        .toggle-switch input {
            opacity: 0;
            width: 100% !important;
            height: 100% !important;
            margin: 0;
            position: absolute;
            top: 0;
            left: 0;
            cursor: pointer;
            z-index: 10;
        }

        .slider {
            z-index: 1;
        }

        .image-manager-container {
            display: flex;
            gap: 20px;
            height: 100%;
            overflow: hidden;
        }

        .menu-sidebar {
            width: 300px;
            background: #fff;
            border-radius: 8px;
            border: 1px solid #eef1f6;
            display: flex;
            flex-direction: column;
        }

        .image-display-area {
            flex: 1;
            background: #fff;
            border-radius: 8px;
            border: 1px solid #eef1f6;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .image-area-header {
            padding: 20px;
            border-bottom: 1px solid #f4f6f8;
            flex-shrink: 0;
        }

        .image-area-body {
            flex: 1;
            padding: 0 20px 20px 20px;
            overflow-y: auto;
        }

        .leaf-menu-item {
            padding: 12px 15px;
            border-bottom: 1px solid #f4f6f8;
            cursor: pointer;
            transition: 0.2s;
            font-size: 14px;
            color: #555;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .leaf-menu-item:hover {
            background: #f8fafc;
            color: #0a3d62;
        }

        .leaf-menu-item.active {
            background: #0a3d62;
            color: #fff;
            border-left: 4px solid #fbc531;
        }

        .leaf-menu-item.active i,
        .leaf-menu-item.active .parent-path {
            color: #fff !important;
            opacity: 0.8;
        }

        .ratio-48-9 {
            width: 100%;
            aspect-ratio: 48 / 9;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .ratio-48-9 img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .meta-info {
            font-size: 11px;
            color: #64748b;
            margin-top: 5px;
            display: flex;
            gap: 10px;
        }

        .update-btn:disabled {
            background: #94a3b8 !important;
            cursor: not-allowed;
        }

        .toggle-switch {
            z-index: 10;
        }
    </style>

    <div class="image-manager-container">
        <div class="menu-sidebar">
            <div style="padding: 15px; border-bottom: 1px solid #eee;">
                <h4 style="margin:0">Menus</h4>
                <small style="color:#888">Select a page to manage images</small>
            </div>
            <div class="menu-tree-wrapper" style="flex: 1; overflow-y: auto;">
                @foreach($leafMenus as $menu)
                    <div class="leaf-menu-item" onclick="loadImages({{ $menu->id }}, this)">
                        <i class="fas fa-file"></i>
                        <div>
                            <div style="font-weight: 500;">{{ $menu->name }}</div>
                            <div class="parent-path" style="font-size: 10px; opacity: 0.6;">{!! $menu->parent_path !!}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="image-display-area" id="imageArea">
            <div
                style="height: 100%; display: flex; align-items: center; justify-content: center; color: #ccc; flex-direction: column;">
                <i class="fas fa-images" style="font-size: 40px; margin-bottom: 10px;"></i>
                <p>Select a menu to manage images.</p>
            </div>
        </div>
    </div>

    <div id="uploadModal" class="modal-overlay">
        <div class="modal-content" style="width: 600px;">
            <button type="button" class="modal-close" onclick="closeModal('uploadModal')"><i
                    class="fas fa-times"></i></button>
            <h3 style="margin:0 0 15px 0; color:#0a3d62">Upload Image</h3>
            <div class="ratio-48-9" id="uploadPreviewContainer"><span style="color:#94a3b8">48:9 Preview</span></div>
            <div class="meta-info" id="uploadMeta"><span>Size: -</span><span>Res: -</span><span>Ratio: -</span></div>
            <div style="color: #e11d48; font-size: 11px; margin-top: 5px;">* Tip: Use 48:9 ratio (e.g. 1920x360px) for best
                fit.</div>
            <form id="uploadForm" style="margin-top:15px; display:flex; flex-direction:column; gap:10px;">
                @csrf
                <input type="file" name="image" onchange="handlePreview(this, 'uploadPreviewContainer', 'uploadMeta')"
                    accept="image/*" required style="padding:10px; border:1px solid #e6e9ee; border-radius:6px;">
                <div style="display:flex; justify-content: flex-end;"><button type="submit" class="update-btn"
                        style="background:#0a3d62; color:#fff; border:none; padding:9px 18px; border-radius:8px; cursor:pointer;">Upload
                        Now</button></div>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal-overlay">
        <div class="modal-content" style="width: 600px;">
            <button type="button" class="modal-close" onclick="closeModal('editModal')"><i
                    class="fas fa-times"></i></button>
            <h3 style="margin:0 0 5px 0; color:#0a3d62">Edit Image</h3>
            <div id="editFileName" style="font-size: 12px; color: #64748b; margin-bottom: 10px; word-break: break-all;">
            </div>
            <div class="ratio-48-9" id="editPreviewContainer"></div>
            <div class="meta-info" id="editMeta"></div>

            <form id="editForm" style="margin-top:15px; display:flex; flex-direction:column; gap:15px;">
                @csrf
                @method('PUT')

                <div style="display:flex; align-items:center; gap:12px;">
                    <label class="toggle-switch" style="cursor: pointer;">
                        <input type="checkbox" id="editActiveToggle" onchange="checkEditChanges()"
                            style="position: absolute; opacity: 0; width: 100%; height: 100%; cursor: pointer; z-index: 5;">
                        <span class="slider"></span>
                    </label>
                    <span id="editStatusLabel" style="font-weight:600; font-size:14px; cursor: default;">Active</span>
                </div>

                <div>
                    <label style="font-size:12px; color:#64748b;">Replace Image (Optional)</label>
                    <input type="file" name="image" onchange="handlePreview(this, 'editPreviewContainer', 'editMeta', true)"
                        accept="image/*"
                        style="width:100%; padding:8px; border:1px solid #e6e9ee; border-radius:6px; margin-top:5px;">
                </div>

                <div style="display:flex; justify-content: flex-end;">
                    <button type="submit" id="editSubmit" class="update-btn" disabled
                        style="background:#0a3d62; color:#fff; border:none; padding:9px 18px; border-radius:8px; cursor:pointer;">Update
                        Image</button>
                </div>
            </form>
        </div>
    </div>

    @include('admin.partials.css')

    @push('scripts')
        <script>
            let currentMenuId = null;
            let currentImageData = null;

            function loadImages(menuId, el) {
                if (el) {
                    document.querySelectorAll('.leaf-menu-item').forEach(i => i.classList.remove('active'));
                    el.classList.add('active');
                }
                currentMenuId = menuId;
                fetch(`/admin/banners/fetch/${menuId}`).then(res => res.json()).then(data => {
                    document.getElementById('imageArea').innerHTML = data.html;
                });
            }

            function handlePreview(input, containerId, metaId, isEdit = false) {
                if (input.files && input.files[0]) {
                    const file = input.files[0];
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const img = new Image();
                        img.onload = function () {
                            document.getElementById(containerId).innerHTML = `<img src="${e.target.result}">`;
                            document.getElementById(metaId).innerHTML = `
                                                                                                                                                                                                                                                                                                                                                        <span>Size: ${(file.size / 1024).toFixed(1)}KB</span>
                                                                                                                                                                                                                                                                                                                                                        <span>Res: ${this.width}x${this.height}px</span>
                                                                                                                                                                                                                                                                                                                                                        <span>Ratio: ${(this.width / this.height).toFixed(2)}:1</span>
                                                                                                                                                                                                                                                                                                                                                    `;
                            if (isEdit) checkEditChanges();
                        };
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            }

            function openEditModal(id, name, path, isActive) {
                currentImageData = { id, name, path, isActive: isActive == 1 };

                document.getElementById('editForm').reset();
                document.getElementById('editFileName').innerText = name;

                const previewImg = new Image();
                previewImg.onload = function () {
                    document.getElementById('editPreviewContainer').innerHTML = `<img src="${previewImg.src}">`;
                    document.getElementById('editMeta').innerHTML = `
                                                                                                                                                                                                                                                                                                                                                <span>Res: ${this.width}x${this.height}px</span>
                                                                                                                                                                                                                                                                                                                                                <span>Ratio: ${(this.width / this.height).toFixed(2)}:1</span>
                                                                                                                                                                                                                                                                                                                                            `;
                };
                previewImg.src = `/storage/${path}?t=${Date.now()}`;

                const toggle = document.getElementById('editActiveToggle');
                toggle.checked = (isActive == 1);

                checkEditChanges();

                const modal = document.getElementById('editModal');
                modal.style.display = 'flex';
                setTimeout(() => modal.classList.add('active'), 10);
            }

            function checkEditChanges() {
                const newActive = document.getElementById('editActiveToggle').checked;
                const hasFile = document.querySelector('#editForm input[type="file"]').files.length > 0;
                const statusChanged = newActive !== currentImageData.isActive;

                document.getElementById('editStatusLabel').innerText = newActive ? 'Active' : 'Inactive';
                document.getElementById('editStatusLabel').style.color = newActive ? '#1e7a43' : '#c0392b';
                document.getElementById('editSubmit').disabled = !(statusChanged || hasFile);
            }

            function openUploadModal() {
                document.getElementById('uploadForm').reset();
                document.getElementById('uploadPreviewContainer').innerHTML = '<span style="color:#94a3b8">48:9 Preview</span>';
                document.getElementById('uploadMeta').innerHTML = '<span>Size: -</span><span>Res: -</span><span>Ratio: -</span>';
                const modal = document.getElementById('uploadModal');
                modal.style.display = 'flex';
                setTimeout(() => modal.classList.add('active'), 10);
            }

            function closeModal(id) {
                const modal = document.getElementById(id);
                modal.classList.remove('active');
                setTimeout(() => modal.style.display = 'none', 300);
            }

            document.getElementById('uploadForm').onsubmit = function (e) {
                e.preventDefault();
                fetch(`/admin/banners/upload/${currentMenuId}`, {
                    method: 'POST', body: new FormData(this), headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                }).then(() => { closeModal('uploadModal'); loadImages(currentMenuId); });
            };

            document.getElementById('editForm').onsubmit = function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('is_active', document.getElementById('editActiveToggle').checked ? 1 : 0);
                fetch(`/admin/banners/${currentImageData.id}`, {
                    method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                }).then(res => res.json()).then(() => {
                    closeModal('editModal');
                    loadImages(currentMenuId);
                });
            };

            function deleteImage(id) {
                if (confirm('Delete?')) fetch(`/admin/banners/${id}`, {
                    method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                }).then(() => loadImages(currentMenuId));
            }
        </script>
    @endpush
@endsection
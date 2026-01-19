@extends('admin.layouts.app')
@section('title', 'Product Management')

@section('content')
    <style>
        .product-manager-container {
            display: flex;
            gap: 20px;
            height: 100%;
            overflow: hidden;
        }

        .generic-sidebar {
            width: 320px;
            background: #fff;
            border-radius: 8px;
            border: 1px solid #eef1f6;
            display: flex;
            flex-direction: column;
        }

        .product-display-area {
            flex: 1;
            background: #fff;
            border-radius: 8px;
            border: 1px solid #eef1f6;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .generic-list-item {
            padding: 12px 15px;
            border-bottom: 1px solid #f4f6f8;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .generic-list-item:hover {
            background: #f8fafc;
        }

        .generic-list-item.active {
            border-left: 4px solid #fbc531;
            background: #0a3d62;
            color: #fff;
        }

        .generic-list-item.active .menu-badge {
            background: #1e7a43;
            color: #fff;
            border: none;
        }

        .modal-scroll {
            max-height: 70vh;
            overflow-y: auto;
            padding-right: 10px;
        }

        .modal-scroll::-webkit-scrollbar {
            width: 5px;
        }

        .modal-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
    </style>

    <div class="product-manager-container">
        <div class="generic-sidebar">
            <div
                style="padding: 15px; border-bottom: 1px solid #eee; display:flex; justify-content:space-between; align-items:center;">
                <h4 style="margin:0">Generics</h4>
                <button onclick="openGenericModal()" class="icon-btn" style="background:#1e7a43; color:#fff;"><i
                        class="fas fa-plus"></i></button>
            </div>
            <div class="menu-tree-wrapper" style="flex: 1; overflow-y: auto;">
                @foreach($generics as $g)
                    <div class="generic-list-item" onclick="loadProducts({{ $g->id }}, this)">
                        <div style="flex:1">
                            <div style="font-weight: 600;">{{ $g->name }}</div>
                            <span class="menu-badge {{ !$g->is_active ? 'inactive' : '' }}"
                                style="font-size:10px; padding:2px 6px;">
                                {{ $g->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div style="display:flex; gap:5px;">
                            <button class="icon-btn" onclick="event.stopPropagation(); openEditGeneric({{ json_encode($g) }})">
                                <i class="fas fa-pen"></i>
                            </button>
                            <button class="icon-btn" style="color:red"
                                onclick="event.stopPropagation(); deleteGeneric({{ $g->id }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="product-display-area" id="productArea">
            <div
                style="height: 100%; display: flex; align-items: center; justify-content: center; color: #ccc; flex-direction: column;">
                <i class="fas fa-capsules" style="font-size: 40px; margin-bottom: 10px;"></i>
                <p>Select a Generic to manage products.</p>
            </div>
        </div>
    </div>

    <div id="genericModal" class="modal-overlay">
        <div class="modal-content" style="width: 400px;">
            <button class="modal-close" onclick="closeModal('genericModal')"><i class="fas fa-times"></i></button>
            <h3 id="genTitle">Add Generic</h3>

            <form id="genForm">
                @csrf
                <div id="genMethod"></div>
                <input type="text" name="name" id="genName" placeholder="Generic Name" required
                    style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; margin-bottom:15px;">

                <div id="genActiveStatus" style="display:none; margin-bottom:15px;">
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_active" id="genActive">
                        <span class="slider"></span>
                    </label>
                    <span style="margin-left:10px; font-weight:600">Active Status</span>
                </div>
                <button type="submit"
                    style="width:100%; background:#0a3d62; color:#fff; border:none; padding:12px; border-radius:8px; cursor:pointer;">
                    Save Generic
                </button>
            </form>
        </div>
    </div>

    <div id="productModal" class="modal-overlay">
        <div class="modal-content" style="width: 800px;">
            <button class="modal-close" onclick="closeModal('productModal')"><i class="fas fa-times"></i></button>
            <h3 id="prodTitle">Add Product</h3>
            <form id="prodForm" enctype="multipart/form-data">
                @csrf
                <div id="prodMethod"></div>
                <div class="modal-scroll">
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                        <div>
                            <label style="font-size:12px; color:#666">Product Image (16:9)</label>
                            <div id="prodPreview"
                                style="aspect-ratio:16/9; background:#f4f6f8; border-radius:6px; margin-bottom:5px; overflow:hidden; display:flex; align-items:center; justify-content:center;">
                                <i class="fas fa-image fa-2x" style="color:#cbd5e1"></i>
                            </div>
                            <input type="file" name="image" onchange="previewProd(this)" style="width:100%">
                        </div>
                        <div style="display:flex; flex-direction:column; gap:10px;">
                            <input type="text" name="trade_name" id="p_trade_name" placeholder="Trade Name (Required)"
                                required style="padding:10px; border:1px solid #ddd; border-radius:6px;">
                            <input type="text" name="preparation" id="p_preparation" placeholder="Preparation"
                                style="padding:10px; border:1px solid #ddd; border-radius:6px;">
                            <input type="text" name="therapeutic_class" id="p_therapeutic_class"
                                placeholder="Therapeutic Class"
                                style="padding:10px; border:1px solid #ddd; border-radius:6px;">
                        </div>
                    </div>

                    @php
                        $fields = [
                            'indications' => 'Indications',
                            'dosage_admin' => 'Dosage & Administration',
                            'use_children' => 'Use in Children',
                            'use_pregnancy_lactation' => 'Use in Pregnancy & Lactation',
                            'contraindications' => 'Contraindications',
                            'precautions' => 'Precautions',
                            'side_effects' => 'Side Effects',
                            'drug_interactions' => 'Drug Interactions',
                            'high_risk' => 'High Risk Groups',
                            'overdosage' => 'Overdosage',
                            'storage' => 'Storage',
                            'presentation' => 'Presentation',
                            'how_supplied' => 'How Supplied',
                            'commercial_pack' => 'Commercial Pack',
                            'packaging' => 'Packaging',
                            'official_specification' => 'Official Specification'
                        ];
                    @endphp

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px; margin-top:15px;">
                        @foreach($fields as $key => $label)
                            <textarea name="{{ $key }}" id="p_{{ $key }}" placeholder="{{ $label }}"
                                style="padding:10px; border:1px solid #ddd; border-radius:6px; height:80px; resize:none;"></textarea>
                        @endforeach
                    </div>
                </div>

                <div style="margin-top:15px; display:flex; justify-content:space-between; align-items:center;">
                    <label id="prodActiveWrapper" style="display:none; align-items:center; gap:10px; cursor:pointer;">
                        <input type="checkbox" name="is_active" id="p_active" value="1">
                        <span style="font-weight:600">Active</span>
                    </label>
                    <button type="submit"
                        style="background:#0a3d62; color:#fff; border:none; padding:12px 30px; border-radius:8px;">Save
                        Product</button>
                </div>
            </form>
        </div>
    </div>

    @include('admin.partials.css')

    <script>
        let currentGenId = null;

        function loadProducts(id, el) {
            document.querySelectorAll('.generic-list-item').forEach(i => i.classList.remove('active'));
            el.classList.add('active');
            currentGenId = id;
            fetch(`/admin/products-actions/fetch/${id}`).then(res => res.json()).then(data => {
                document.getElementById('productArea').innerHTML = data.html;
            });
        }

        let currentEditGenericId = null;

        function openGenericModal() {
            currentEditGenericId = null;
            document.getElementById('genForm').reset();
            document.getElementById('genTitle').innerText = "Add Generic";
            document.getElementById('genActiveStatus').style.display = "none";
            document.getElementById('genericModal').style.display = 'flex';
            setTimeout(() => document.getElementById('genericModal').classList.add('active'), 10);
        }

        function openEditGeneric(g) {
            currentEditGenericId = g.id;
            document.getElementById('genTitle').innerText = "Edit Generic";
            document.getElementById('genName').value = g.name;
            document.getElementById('genActive').checked = (g.is_active == 1);
            document.getElementById('genActiveStatus').style.display = "block";

            document.getElementById('genericModal').style.display = 'flex';
            setTimeout(() => document.getElementById('genericModal').classList.add('active'), 10);
        }

        document.getElementById('genForm').onsubmit = function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            const url = currentEditGenericId
                ? `/admin/products-actions/generic-update/${currentEditGenericId}`
                : `/admin/products-actions/generic-store`;

            if (currentEditGenericId) {
                formData.append('_method', 'PUT');
            }

            formData.set('is_active', document.getElementById('genActive').checked ? 1 : 0);

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
                .then(res => {
                    if (!res.ok) return res.json().then(json => { throw json });
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        closeModal('genericModal');
                        window.location.reload();
                    }
                })
                .catch(err => {
                    alert(err.error || 'Operation failed. Check if name is unique.');
                    console.error(err);
                });
        };

        function deleteGeneric(id) {
            if (confirm('Delete this Generic? This will delete ALL associated products and images permanently.')) {
                fetch(`/admin/products-actions/generic-delete/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) window.location.reload();
                        else alert('Error: ' + data.error);
                    });
            }
        }

        function openAddProduct() {
            document.getElementById('prodForm').reset();
            document.getElementById('prodTitle').innerText = "Add Product to Generic";
            document.getElementById('prodMethod').innerHTML = "";
            document.getElementById('prodActiveWrapper').style.display = "none";
            document.getElementById('prodPreview').innerHTML = '<i class="fas fa-image fa-2x" style="color:#cbd5e1"></i>';
            document.getElementById('productModal').style.display = 'flex';
            setTimeout(() => document.getElementById('productModal').classList.add('active'), 10);
        }

        function openEditProduct(p) {
            document.getElementById('prodTitle').innerText = "Edit Product";
            document.getElementById('prodMethod').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('prodActiveWrapper').style.display = "flex";

            for (let key in p) {
                let input = document.getElementById('p_' + key);
                if (input) input.value = p[key];
            }
            document.getElementById('p_active').checked = p.is_active == 1;
            document.getElementById('prodPreview').innerHTML = `<img src="/storage/${p.image_path}" style="width:100%; height:100%; object-fit:cover">`;

            document.getElementById('prodForm').dataset.id = p.id;
            document.getElementById('productModal').style.display = 'flex';
            setTimeout(() => document.getElementById('productModal').classList.add('active'), 10);
        }

        function previewProd(input) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = e => document.getElementById('prodPreview').innerHTML = `<img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover">`;
                reader.readAsDataURL(input.files[0]);
            }
        }

        document.getElementById('prodForm').onsubmit = function (e) {
            e.preventDefault();
            let id = this.dataset.id;
            let url = id ? `/admin/products-actions/product-update/${id}` : `/admin/products-actions/product-store/${currentGenId}`;
            let formData = new FormData(this);
            if (id) formData.append('is_active', document.getElementById('p_active').checked ? 1 : 0);

            fetch(url, {
                method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(() => { closeModal('productModal'); loadProducts(currentGenId, document.querySelector('.generic-list-item.active')); });
        };

        function deleteProduct(id) {
            if (confirm('Delete product?')) fetch(`/admin/products-actions/product-delete/${id}`, {
                method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(() => loadProducts(currentGenId, document.querySelector('.generic-list-item.active')));
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
            setTimeout(() => document.getElementById(id).style.display = 'none', 300);
        }
    </script>
@endsection
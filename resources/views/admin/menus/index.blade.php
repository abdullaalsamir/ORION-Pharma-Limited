@extends('admin.layouts.app')

@section('title', 'Menus')

@section('content')
    <div class="card">

        <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;gap:16px;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div>
                    <h3 style="margin:0 0 4px 0;">Menu Management</h3>
                    <small style="color:#666">Create and organize your site menus, submenus and sub-submenus.</small>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.menus.store') }}" class="add-menu-form"
                style="display:flex;align-items:center;gap:8px;">
                @csrf
                <input type="text" name="name" placeholder="Menu name" required
                    style="padding:9px 12px;border:1px solid #e6e9ee;border-radius:6px;min-width:180px;">
                <select name="parent_id" style="padding:9px 12px;border:1px solid #e6e9ee;border-radius:6px;">
                    <option value="">⁝⁝⁝ Main Menu ⁝⁝⁝</option>
                    @foreach($menus as $menu)
                        <option value="{{ $menu->id }}">{{ $menu->name }}</option>
                        @foreach($menu->children as $child)
                            <option value="{{ $child->id }}">— {{ $child->name }}</option>
                        @endforeach
                    @endforeach
                </select>

                <button title="Add menu"
                    style="background:#0a3d62;border:none;color:#fff;padding:9px 11px;border-radius:6px;cursor:pointer;">
                    <i class="fas fa-plus"></i>
                </button>
            </form>
        </div>

        <div class="card-body" style="margin-top:18px; flex: 1; overflow: hidden; display: flex; flex-direction: column;">
            <div class="menu-tree-wrapper">
                @if($menus->count() == 0)
                    <p style="color:#666">No menus yet — add your first menu using the form above.</p>
                @endif

                <ul class="menu-tree">
                    @foreach($menus as $menu)
                        @include('admin.menus.partials.menu-item', ['menu' => $menu])
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div id="editModal" class="modal-overlay">
        <div class="modal-content">
            <button type="button" class="modal-close" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>

            <h3 style="margin:0 0 12px 0;color:#0a3d62">Edit Menu</h3>

            <form method="POST" id="editForm" style="display:flex;flex-direction:column;gap:10px;">
                @csrf
                @method('PUT')

                <label style="font-size:13px;color:#555">Name</label>
                <input type="text" name="name" id="editName" required
                    style="padding:10px;border:1px solid #e6e9ee;border-radius:6px;">

                <label style="font-size:13px;color:#555">Parent</label>
                <select name="parent_id" id="editParent" style="padding:10px;border:1px solid #e6e9ee;border-radius:6px;">
                    <option value="">⁝⁝⁝ Main Menu ⁝⁝⁝</option>
                    @foreach($menus as $m)
                        <option value="{{ $m->id }}" data-active="{{ $m->is_active ? '1' : '0' }}">{{ $m->name }}</option>
                        @foreach($m->children as $c)
                            <option value="{{ $c->id }}" data-active="{{ $c->is_active ? '1' : '0' }}">
                                — {{ $c->name }}
                            </option>
                        @endforeach
                    @endforeach
                </select>

                <label style="display:flex;align-items:center;gap:10px;margin-top:10px;">
                    <div class="toggle-switch">
                        <input type="checkbox" name="is_active" id="editActive">
                        <span class="slider"></span>
                    </div>
                    <span id="toggleLabel" style="font-weight:600;"></span>
                </label>

                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button type="submit"
                        style="background:#0a3d62;color:#fff;border:none;padding:9px 14px;border-radius:8px;cursor:pointer;transition:0.3s;"
                        onmouseover="this.style.background='#1e6091'" onmouseout="this.style.background='#0a3d62'"
                        onmousedown="this.style.background='#074173'" onmouseup="this.style.background='#1e6091'">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    @include('admin.partials.menu-tree-css')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('editModal');
            const form = document.getElementById('editForm');
            const editParent = document.getElementById('editParent');
            const toggleInput = document.getElementById('editActive');
            const toggleLabel = document.getElementById('toggleLabel');

            function updateToggleState() {
                if (!editParent || !toggleInput) return;

                const selectedOption = editParent.options[editParent.selectedIndex];
                const isParentInactive = selectedOption.value !== "" && selectedOption.dataset.active === "0";

                if (isParentInactive) {
                    toggleInput.checked = false;
                    toggleInput.disabled = true;
                    toggleInput.parentElement.style.opacity = '0.5';
                    toggleInput.parentElement.style.pointerEvents = 'none';
                    toggleLabel.textContent = 'Inactive (Locked by Parent)';
                    toggleLabel.style.color = '#c0392b';
                } else {
                    toggleInput.disabled = false;
                    toggleInput.parentElement.style.opacity = '1';
                    toggleInput.parentElement.style.pointerEvents = 'auto';
                    toggleLabel.textContent = toggleInput.checked ? 'Active' : 'Inactive';
                    toggleLabel.style.color = toggleInput.checked ? '#1e7a43' : '#c0392b';
                }
            }

            document.querySelectorAll('.edit-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    form.action = `/admin/menus/${this.dataset.id}`;

                    document.getElementById('editName').value = this.dataset.name;
                    editParent.value = this.dataset.parent || '';
                    toggleInput.checked = this.dataset.active == '1';

                    updateToggleState();

                    modal.style.display = 'flex';
                    setTimeout(() => modal.classList.add('active'), 10);
                });
            });

            editParent.addEventListener('change', updateToggleState);
            toggleInput.addEventListener('change', updateToggleState);

            window.closeModal = function () {
                modal.classList.remove('active');
                setTimeout(() => modal.style.display = 'none', 300);
            };

            document.querySelectorAll('.collapse-toggle').forEach(btn => {
                btn.addEventListener('click', function () {
                    const target = document.querySelector(this.dataset.target);
                    if (!target) return;

                    if (target.classList.contains('expanded')) {
                        target.classList.remove('expanded');
                        this.innerHTML = '<i class="fas fa-chevron-right"></i>';

                        target.querySelectorAll('.nested').forEach(c => c.classList.remove('expanded'));
                        target.querySelectorAll('.collapse-toggle').forEach(b => b.innerHTML = '<i class="fas fa-chevron-right"></i>');
                    } else {
                        target.classList.add('expanded');
                        this.innerHTML = '<i class="fas fa-chevron-down"></i>';
                    }
                });
            });

            let draggedItem = null;
            const allCards = document.querySelectorAll('.menu-card');

            document.querySelectorAll('.drag-handle').forEach(handle => {
                const card = handle.closest('.menu-card');
                const item = card.closest('li');

                handle.addEventListener('dragstart', (e) => {
                    draggedItem = item;
                    card.classList.add('dragging');
                    e.dataTransfer.setData('text/plain', item.dataset.id);
                });

                handle.addEventListener('dragend', () => {
                    card.classList.remove('dragging');
                    allCards.forEach(c => c.classList.remove('drag-over-above', 'drag-over-below'));
                    saveMenuOrder();
                });
            });

            allCards.forEach(card => {
                card.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    const item = card.closest('li');
                    if (!draggedItem || draggedItem === item) return;

                    const rect = card.getBoundingClientRect();
                    const isAbove = e.clientY < rect.top + rect.height / 2;

                    allCards.forEach(c => c.classList.remove('drag-over-above', 'drag-over-below'));
                    card.classList.add(isAbove ? 'drag-over-above' : 'drag-over-below');
                });

                card.addEventListener('drop', (e) => {
                    e.preventDefault();
                    const item = card.closest('li');
                    if (!draggedItem || draggedItem === item) return;

                    const rect = card.getBoundingClientRect();
                    const isAbove = e.clientY < rect.top + rect.height / 2;
                    const parent = item.parentNode;

                    if (isAbove) {
                        parent.insertBefore(draggedItem, item);
                    } else {
                        parent.insertBefore(draggedItem, item.nextSibling);
                    }
                    draggedItem.dataset.parent = item.dataset.parent;
                });
            });

            function saveMenuOrder() {
                const menus = [];
                function process(ul, parentId) {
                    Array.from(ul.children).forEach((li, index) => {
                        menus.push({
                            id: li.dataset.id,
                            parent_id: parentId,
                            sort_order: index
                        });
                        const sub = li.querySelector(':scope > .nested > ul');
                        if (sub) process(sub, li.dataset.id);
                    });
                }
                process(document.querySelector('.menu-tree'), null);

                fetch('{{ route("admin.menus.update-order") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ menus })
                });
            }
        });
    </script>
@endsection
@extends('admin.layouts.app')

@section('title', 'Menus')

@section('content')
    <div class="card">

        <h2>Menu Management</h2>

        <!-- ADD MENU -->
        <form method="POST" action="{{ route('admin.menus.store') }}" style="margin-bottom:20px;">
            @csrf
            <input type="text" name="name" placeholder="Menu name" required>
            <select name="parent_id">
                <option value="">— Main Menu —</option>
                @foreach($menus as $menu)
                    <option value="{{ $menu->id }}">{{ $menu->name }}</option>
                    @foreach($menu->children as $child)
                        <option value="{{ $child->id }}">— {{ $child->name }}</option>
                    @endforeach
                @endforeach
            </select>
            <button>Add</button>
        </form>

        <!-- MENU TREE -->
        <ul class="menu-tree">
            @foreach($menus as $menu)
                @include('admin.menus.partials.menu-item', ['menu' => $menu])
            @endforeach
        </ul>

    </div>

    <style>
        .menu-tree ul {
            margin-left: 25px;
        }

        .menu-tree li {
            margin: 8px 0;
        }

        .menu-actions {
            font-size: 13px;
        }

        .menu-actions a {
            margin-right: 10px;
        }
    </style>
@endsection

<!-- EDIT MODAL -->
<div id="editModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5);">
    <div style="background:#fff; width:400px; margin:10% auto; padding:20px; border-radius:8px;">
        <h3>Edit Menu</h3>

        <form method="POST" id="editForm">
            @csrf
            @method('PUT')

            <input type="text" name="name" id="editName" required>

            <select name="parent_id" id="editParent">
                <option value="">— Main Menu —</option>
                @foreach($menus as $m)
                    <option value="{{ $m->id }}">{{ $m->name }}</option>
                    @foreach($m->children as $c)
                        <option value="{{ $c->id }}">— {{ $c->name }}</option>
                    @endforeach
                @endforeach
            </select>

            <label>
                <input type="checkbox" name="is_active" id="editActive">
                Active
            </label>

            <div style="margin-top:15px;">
                <button type="submit">Update</button>
                <button type="button" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const modal = document.getElementById('editModal');
        const form = document.getElementById('editForm');

        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function () {

                form.action = `/admin/menus/${this.dataset.id}`;

                document.getElementById('editName').value = this.dataset.name;
                document.getElementById('editParent').value = this.dataset.parent || '';
                document.getElementById('editActive').checked = this.dataset.active == 1;

                modal.style.display = 'block';
            });
        });

        window.closeModal = function () {
            modal.style.display = 'none';
        };

    });
</script>
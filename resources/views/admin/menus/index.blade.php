@extends('admin.layouts.app')
@section('title', 'Menu Management')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header items-start!">
            <div class="flex flex-col">
                <h1>Menu Management</h1>
                <p class="text-xs text-slate-400 font-medium">Organize hierarchical site navigation</p>
            </div>

            <form method="POST" action="{{ route('admin.menus.store') }}"
                class="flex items-center gap-3 bg-slate-50 p-1.5 rounded-2xl border border-slate-100">
                @csrf
                <input type="text" name="name" placeholder="Menu Name..." required class="input-field h-10! w-48">

                <select name="parent_id" class="input-field h-10! w-40">
                    <option value="">⁝⁝⁝ Main Menu ⁝⁝⁝</option>
                    @foreach($menus as $menu)
                        <option value="{{ $menu->id }}">{{ $menu->name }}</option>
                    @endforeach
                </select>

                <div class="flex bg-white p-1 rounded-xl border border-slate-200 h-10 items-center">
                    <label class="cursor-pointer">
                        <input type="radio" name="is_multifunctional" value="0" checked class="peer sr-only">
                        <span
                            class="px-3 py-1.5 rounded-lg text-[10px] font-bold text-slate-400 peer-checked:bg-admin-blue peer-checked:text-white transition-all uppercase">Functional</span>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="is_multifunctional" value="1" class="peer sr-only">
                        <span
                            class="px-3 py-1.5 rounded-lg text-[10px] font-bold text-slate-400 peer-checked:bg-admin-blue peer-checked:text-white transition-all uppercase">Multi</span>
                    </label>
                </div>

                <button type="submit" class="btn-primary h-10!">
                    <i class="fas fa-plus"></i> Add Menu
                </button>
            </form>
        </div>

        <div class="admin-card-body custom-scrollbar bg-slate-50/20">
            <ul id="menu-sortable" class="nested-sortable-list space-y-4">
                <li class="p-4 bg-white border border-slate-200 rounded-2xl flex items-center justify-between opacity-60">
                    <div class="flex items-center gap-4 pl-10">
                        <span class="font-bold text-slate-700">{{ $homeMenu->name }}</span>
                        <span class="badge badge-info">System Default</span>
                    </div>
                </li>

                @foreach($menus as $menu)
                    @include('admin.menus.partials.menu-item', ['menu' => $menu])
                @endforeach
            </ul>
        </div>
    </div>

    <div id="editModal" class="modal-overlay hidden">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-8 pb-2 border-b border-slate-100">
                <h1 class="mb-0!">Edit Menu Item</h1>
                <button onclick="closeModal()" class="btn-icon"><i class="fas fa-times text-xl"></i></button>
            </div>

            <form method="POST" id="editForm" class="space-y-6">
                @csrf @method('PUT')

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Menu Name</label>
                        <input type="text" name="name" id="editName" required class="input-field">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[11px] font-bold text-slate-400 uppercase ml-1">Parent Category</label>
                        <select name="parent_id" id="editParent" class="input-field">
                            <option value="">⁝⁝ Main Menu ⁝⁝⁝</option>
                            @foreach($menus as $m)
                                <option value="{{ $m->id }}">{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between">
                    <label class="toggle-switch">
                        <input type="checkbox" id="editActive" name="is_active">
                        <div class="toggle-bg"></div>
                        <span id="toggleLabel" class="...">Active</span>
                    </label>

                    <div class="flex bg-white p-1 rounded-xl border border-slate-200">
                        <label class="cursor-pointer">
                            <input type="radio" name="is_multifunctional" value="0" id="edit-type-functional"
                                class="peer sr-only">
                            <span
                                class="px-5 py-2 rounded-lg text-[10px] font-bold text-slate-400 peer-checked:bg-admin-blue peer-checked:text-white transition-all uppercase">Functional</span>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="is_multifunctional" value="1" id="edit-type-multi"
                                class="peer sr-only">
                            <span
                                class="px-5 py-2 rounded-lg text-[10px] font-bold text-slate-400 peer-checked:bg-admin-blue peer-checked:text-white transition-all uppercase">Multi</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="btn-primary px-12! h-12!">Update Menu
                        Item</button>
                </div>
            </form>
        </div>
    </div>
@endsection
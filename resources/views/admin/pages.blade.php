@extends('admin.layouts.app')

@section('title', 'Pages')

@section('content')
    <div class="card">

        <div class="card-header">
            <h3 style="margin:0 0 4px 0;">Page Management</h3>
            <small style="color:#666">
                Edit content for menu, submenu and sub-submenu pages.
            </small>
        </div>

        <div class="card-body" style="margin-top:18px; flex: 1; overflow: hidden; display: flex; flex-direction: column;">
            <div class="menu-tree-wrapper">
                <ul class="menu-tree">
                    @foreach($menus as $menu)
                        @include('admin.pages.partials.page-item', ['menu' => $menu])
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div id="pageModal" class="modal-overlay">
        <div class="modal-content">
            <button class="modal-close" onclick="closePageModal()">
                <i class="fas fa-times"></i>
            </button>

            <h3 style="margin:0 0 12px 0;color:#0a3d62">Edit Page Content</h3>

            <textarea id="pageContent"
                style="width:100%;min-height:220px;padding:12px;border:1px solid #e6e9ee;border-radius:6px;"></textarea>

            <div style="text-align:right;margin-top:12px;">
                <button id="savePage"
                    style="background:#0a3d62;color:#fff;border:none;padding:9px 14px;border-radius:8px;cursor:pointer;transition:0.3s;"
                    onmouseover="this.style.background='#1e6091'" onmouseout="this.style.background='#0a3d62'"
                    onmousedown="this.style.background='#074173'" onmouseup="this.style.background='#1e6091'">
                    Update
                </button>
            </div>
        </div>
    </div>

    @include('admin.partials.menu-tree-css')
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                let currentPageId = null;

                document.querySelectorAll('.collapse-toggle').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const target = document.querySelector(this.dataset.target);
                        if (!target) return;

                        if (target.classList.contains('expanded')) {
                            target.classList.remove('expanded');
                            this.innerHTML = '<i class="fas fa-chevron-right"></i>';

                            target.querySelectorAll('.nested').forEach(c => c.classList.remove('expanded'));
                            target.querySelectorAll('.collapse-toggle').forEach(b =>
                                b.innerHTML = '<i class="fas fa-chevron-right"></i>'
                            );
                        } else {
                            target.classList.add('expanded');
                            this.innerHTML = '<i class="fas fa-chevron-down"></i>';
                        }
                    });
                });

                document.querySelectorAll('.edit-page').forEach(btn => {
                    btn.addEventListener('click', () => {
                        currentPageId = btn.dataset.id;
                        document.getElementById('pageContent').value = btn.dataset.content || '';
                        openPageModal();
                    });
                });

                const saveBtn = document.getElementById('savePage');
                if (saveBtn) {
                    saveBtn.addEventListener('click', () => {
                        fetch(`/admin/pages/${currentPageId}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                content: document.getElementById('pageContent').value
                            })
                        })
                            .then(response => {
                                if (!response.ok) throw new Error('Save failed');
                                return response.json();
                            })
                            .then(() => {
                                window.location.reload();
                            });
                    });
                }

                window.openPageModal = function () {
                    const modal = document.getElementById('pageModal');
                    modal.style.display = 'flex';
                    setTimeout(() => modal.classList.add('active'), 10);
                };

                window.closePageModal = function () {
                    const modal = document.getElementById('pageModal');
                    modal.classList.remove('active');
                    setTimeout(() => modal.style.display = 'none', 300);
                };

            });
        </script>
    @endpush
@endsection